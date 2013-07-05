<?php
interface readPackage {
    public function __construct($dir);
    public function open();
    public function file_exists($file);
    public function read_file($file);
    public function file_info($idx);
}

class ReadFolder implements readPackage {

    private $base_dir;
    private $entries = array();
    private $valid = false;
    
    private function read_sub_folder($s) {
        $d = dir($s);
        while($file = $d->read()) {
            if (substr($file, 0, 1) != '.') {
                $fullpath = $d->path.'/'.$file;
                if (is_file($fullpath)) {
                    $this->entries[] = array('name'=>substr($fullpath, strlen($this->base_dir)+1), 'size'=>filesize($fullpath));
                } elseif (is_dir($fullpath)) {
                    $this->entries[] = array('name'=>substr($fullpath, strlen($this->base_dir)+1).'/', 'size'=>0);
                    $this->read_sub_folder($fullpath);
                }
            }
        }
    }

    public function __construct($dir) {
        $this->base_dir = (substr($dir, -1) == '/') ? substr($dir, 0, -1) : $dir;
    }
    
    public function open() {
        if (is_dir($this->base_dir)) {
            $this->read_sub_folder($this->base_dir);
            $this->valid = true;
        } else {
            $this->valid = false;
        }
        return $this->valid;
    }
    
    public function file_exists($file) {
        return file_exists($this->base_dir.'/'.$file);
    }

    public function read_file($file) {
        return is_file($this->base_dir.'/'.$file) ? file_get_contents($this->base_dir.'/'.$file) : false;
    }
    
    public function file_info($idx) {
        return isset($this->entries[$idx]) ? array('name'=>$this->entries[$idx]['name'], 'size'=>$this->entries[$idx]['size']) : false;
    }

}

class ReadZip implements readPackage {

    private $valid = false;
    public $zip;
    private $file;
    
    function __construct($file) {
        $this->file = $file;
    }
    
    function open() {
        $this->zip = new ZipArchive();
        return $this->valid = $this->zip->open($this->file);
    }

    function file_exists($file) {
        return $this->zip->locateName($file) !== false ? true : false;
    }
    
    function file_info($idx) {
        $stat = $this->zip->statIndex($idx);
        return $stat ? array('name'=>$stat['name'], 'size'=>$stat['size']) : false;
    }

    function read_file($file) {
        return $this->zip->getFromName($file);
    }

}

class PackageManager {
    
    /**
     * @var bool $remote_pkg Is the package file on a remote server?
     */
    private $remote_pkg = false;

    /**
     * @var array $error_msg Error messages
     */
    private $error_msgs = array();
    
    /**
     * Zip (0) or folder (1) ?
     */
    private $mode;

    /**
     * @var Either a ReadFolder or a ReadZip object
     */
    public $package;
    
    /**
     * @var object $core Core, DocumentParser, or Install object
     */
    private $core;
    
    /**
     * Files/folders/elements to install
     */
    private $new_folders = array();
    private $new_files = array();
    private $modified_files = array();
    private $elements = array();
    private $not_writable = array();

    /**
     * @var string $file The filename of the copy of the zip file or the folder that we are working on.
     */
    public $file;

    /**
     * @var string $name A name, as good as we can determine.
     *
     * A filename for manual uploads or a URL for remote packages. Later if XML package files implemented we can get the info from this.
     */
    public $name;

    /**
     * @var bool $haspackage Have we got a readable package? WARNING: This does not mean the package is valid or correct.
     */
    public $haspackage = false;
    
    /**
     * @var string $packageXML package.xml contents
     */
    public $packageXML;

    /**
     * @var string $README readme file contents
     */
    public $README = '';

    /**
     * @var string $changelog Changelog file contents
     */
    public $changelog = '';

    /**
     * @var string $summary Summary of installation, as HTML
     */
    public $summary = '';

    /**
     * @var string $summary Summary of installation process, as HTML
     */
    public $install_summary = '';

    /**
     * @var string $auto_install_code Code for plugin for auto-installation
     */
    public $auto_install_code;

    /**
     * Fetches remote file if applicable.
     * Extracts readme, changelog, and XML file.
     * 
     * @param object $core A Core, Install or DocumentParser object.
     * @param string $file
     */
    function __construct($core, $file, $name = null) {
    
        $this->core = $core;
        $this->name = $name ? $name : $file;

        if (preg_match('/^https?\:\/\//', $file)) {
        
            if (!class_exists('ZipArchive')) {
                $this->error_msgs[] = 'php_zip not installed/enabled';
            }
            
            if (!in_array('curl', get_loaded_extensions())) {
                $this->error_msgs[] = 'php_curl not installed/enabled';
            }

            $this->mode = 0;

            if (!sizeof($this->error_msgs)) {

                $remote_pkg = true;
                
                // Fetch remote package
                $cr = curl_init($file);
                curl_setopt($cr, CURLOPT_RETURNTRANSFER, true);
            
                $this->file = tempnam(sys_get_temp_dir(), 'clp');
                $fr2 = fopen($this->file, 'w');
                
                if ($fr2) {
                    if ($contents = curl_exec($cr)) {
                        fwrite($fr2, $contents);
                    } else {
                        $this->error_msgs[] = 'Error fetching remote file';
                    }
                } else {
                    $this->error_msgs[] = 'Error opening temporary file to receive remote package';
                }
            }

        } elseif (is_file($file)) {
        
            $this->mode = 0;

            if (!class_exists('ZipArchive')) {
                $this->error_msgs[] = 'php_zip not installed/enabled';
            } else {
                if (is_uploaded_file($file)) {
                    $this->file = tempnam(sys_get_temp_dir(), 'clp');
                    move_uploaded_file($file, $this->file);
                } else {
                    $this->file = $file;
                }
            }

        } elseif (is_dir($modx->config['base_path'].$file)) {

            $this->mode = 1;
            $this->file = $modx->config['base_path'].$file;

        }

        if (!sizeof($this->error_msgs)) {
    
            $this->package = $this->mode ? new ReadFolder($this->file) : new ReadZip($this->file);
    
            if ($this->package->open() === true) {
            
                $this->haspackage = true; // We have a readable package
                
                // Optional files
                $this->README = $this->package->read_file('README.txt');
                if (!$this->README) $this->README = $this->package->read_file('README');
                $this->changelog = $this->package->read_file('changelog.txt');
                $this->packageXML = $this->package->read_file('package.xml');
                
            } else {
                $this->error_msgs[] = 'Error opening package';
            }
        }
    
        if (isset($fr2) && $fr2) {
            fclose($fr2);
        }
    }
    
    /**
     * Is there an error so far?
     *
     * @return bool
     */
    function is_error() {
        return (bool)sizeof($this->error_msgs);
    }
    
    /**
     * Get an array of error messages
     *
     * @return array
     */
    function errors() {
        return $this->error_msgs;
    }
    
    /**
     * Is there a permissions error?
     */
    function perms_error() {
        return sizeof($this->not_writable) ? true : false;
    }
    
    /**
     * Is this a file we will install as is, either by creating a new file or overwriting an old one?
     *
     * Ignores files ending in ~, .bak, and .save.
     *
     * @param string $file
     * @return bool
     */
    private function is_file_to_install($file) {
        return substr($file, -1) != '~' && substr($file, -4) != '.bak' && substr($file, -5) != '.save';
    }
    
    /** 
     * Get name and description from an element template
     *
     * @param string $file
     * @return array
     */
    private function get_name_and_desc($body) {
        // Name and description
        preg_match_all('/[\n\r]\s*\*\s*([a-z][^\n\r]+)/i', $body, $matches);
        if (isset($matches[1][0])) {
            $name = $matches[1][0];
            if (isset($matches[1][1])) {
                $desc = $matches[1][1];
            }
        }
        return array('name'=>$name, 'desc'=>$desc);
    }

    /**
     * Prepare for eval by stripping start and end PHP tags, if present
     *
     * @param string $code
     * @return string
     */
    private function prepare_for_eval($code) {
        return preg_replace('/\?\>\s*$/', '', preg_replace('/^\s*(\/\/)?\s*\<\?php/', '', $code));
    }

    /**
     * Disable legacy elements
     */
    function disable_legacy_elements($type, $tbl, $name, $legacy_names) {
        $output = '';
        // Disable plugins with legacy names
        if ($legacy_names) {
            if (!$this->core->db->update('disabled=1', $tbl, 'name IN (\''.implode('\',\'', preg_split('/\s*,\s*/', $legacy_names, -1, PREG_SPLIT_NO_EMPTY)).'\')')) {
                $output .= "<p class=\"error\">Error removing legacy $type now replaced by plugin $name</p>";
                return false;
            }
            $output .= "<p>Removing legacy plugins now replaced by plugin $name</p>";
        }
        return $output;
    }

    /**
     * Summarise the installation to do.
     */
    function summarise() {
    
        if ($this->package->file_exists('install.php') !== false) {
            $this->summary .= '<h3>Install script</h3><p class="warning">This package contains an install script that will be run when all files and elements have been installed.</p>';
        }

        $i = 0;
        while($stat = $this->package->file_info($i++)) {
        
            if (substr($stat['name'], 0, 6) == 'files/' && strlen($stat['name']) > 6) {
            
                $filename = substr($stat['name'], 6);
                
                $fullpath = $this->core->config['base_path'].$filename;
        
                if (substr($filename, -1) == '/') {
                    // Folder
                    if (!is_dir($fullpath)) {
                        $this->new_folders[] = $filename;
                        $dir = preg_replace('~/[^/]+/$~', '/', $filename);
                        if (is_dir($this->core->config['base_path'].$dir) && !is_writable($this->core->config['base_path'].$dir)) {
                            $this->not_writable[] = $dir;
                        }
                    }
                } elseif ($this->is_file_to_install($filename)) {
                    // File
                    if (!is_file($this->core->config['base_path'].$filename)) {
                        $this->new_files[] = $filename;
                        $dir = preg_replace('~/[^/]+$~', '', $filename);
                        if (is_dir($this->core->config['base_path'].$dir) && !is_writable($this->core->config['base_path'].$dir)) {
                            $this->not_writable[] = $dir;
                        }
                    } else {
                        $this->modified_files[] = $filename;
                        if (!is_writable($fullpath)) {
                            $this->not_writable[] = $fullpath;
                        }
                    }
                }

            } elseif (preg_match('/^(chunks|modules|plugins|snippets|templates|tvs)\/(.+)$/', $stat['name'], $matches) && $this->is_file_to_install($matches[2])) {
                $this->elements[$matches[1]][] = $matches[2];
            }

        }
        
        if (sizeof($this->new_folders)) {
            sort($this->new_folders);
            $this->summary .= '<h3>New Folders</h3>'.implode('<br />', $this->new_folders);
        }
        
        if (sizeof($this->new_files)) {
            $this->summary .= '<h3>New Files</h3>'.implode('<br />', $this->new_files);
        }
        
        if (sizeof($this->modified_files)) {
            $this->summary .= '<h3>Modified Files</h3>'.implode('<br />', $this->modified_files);
        }

        if (sizeof($this->not_writable)) {
            $this->summary .= '<h3 class="error">These files/folders are not writable! Check permissions on:</h3>'.implode('<br />', $this->not_writable);
            $this->error_msgs[] = 'Some files or folders are not writable.';
        }

        foreach($this->elements as $el_category => $els) {
            $this->summary .= '<h3>'.ucfirst($el_category).'</h3><ul>';
            foreach($els as $el) {
                extract($this->get_name_and_desc($this->package->read_file($el_category.'/'.$el)));
                $this->summary .= '<li>'.$name.' - '.$desc.'</li>';
            }
            $this->summary .= '</ul>';
        }

        if ($this->mode == 1) {
            $this->auto_install_code =
"require('".__FILE__."');
\$PM = new PackageManager(\$modx, '{$this->file}');
if (\$PM->haspackage && !\$PM->is_error()) {
    \$PM->summarise();
    if (!\$PM->is_error()) \$PM->install();
}
";
        }
    }
    
    /** 
     * Install
     */
    function install() {
    
        $this->install_summary = '';
    
        $this->package = $this->mode ? new ReadFolder($this->file) : new ReadZip($this->file);

        if ($this->package->open() === true) {

            foreach($this->new_folders as $folder) {
                if (!mkdir($this->core->config['base_path'].$folder)) {
                    $this->install_summary .= "<p class=\"error\">Error creating directory $folder</p>";
                    return false;
                }
                $this->install_summary .= "<p>Creating directory $folder</p>";
            }
            
            foreach($this->new_files as $file) {
                if (file_put_contents($this->core->config['base_path'].$file, $this->package->read_file('files/'.$file)) == -1) {
                    $this->install_summary .= "<p class=\"error\">Error writing new file $file</p>";
                    return false;
                }
                $this->install_summary .= "<p>Writing new file $file</p>";
            }

            foreach($this->modified_files as $file) {
                if (file_put_contents($this->core->config['base_path'].$file, $this->package->read_file('files/'.$file)) == -1) {
                    $this->install_summary .= "<p class=\"error\">Error updating existing file $file</p>";
                    return false;
                }
                $this->install_summary .= "<p>Updating existing file $file</p>";
            }
            
            foreach($this->elements as $el_category => $els) {
            
                foreach($els as $el) {
                    $full = $this->package->read_file($el_category.'/'.$el);
                    
                    // Fix for install files with only line feeds marking the ends of lines
                    $full = str_replace("\r\n", "\n", $full);
                    $full = str_replace("\r", "\n", $full);

                    // Get @category - check with $el_category
                    preg_match('/@category\s+([a-z]+)/', $full, $matches);
                    $el_category_singular = $matches[1];
                    if ($el_category_singular.'s' == $el_category) {

                        extract($this->get_name_and_desc($full));
                        
                        // Get version number                                                // <<<< Not functionally used - only for description
                        preg_match('/@version\s+((clipper-)?[0-9.]+)/', $full, $matches);   // <<<< alpha, beta, rc not yet implemented
                        $full_version = $version = $matches[1];
                        if (substr($version, 0, 8) == 'clipper-') {
                            $clipper_version = true;
                            $version = substr($version, 8);
                        } else {
                            $clipper_version = false;
                        }

                        if (isset($name) && $version) {
                        
                            $flds = array();

                            // Get @internals
                            $internals = array();
                            preg_match_all('/@internal\s+@([^\s]+) +(.+)/m', $full, $matches, PREG_SET_ORDER); // <<<< preg modifiers
                            foreach($matches as $match) {
                                $internals[$match[1]] = $match[2];
                            }

                            // @internal @overwrite true/false - defaults to true
                            $overwrite = !isset($internals['overwrite']) || $internals['overwrite'] != 'false';
                            
                            // Content, removing phpdoc block
                            $content = preg_replace('~/\*.*?\*/~s', '', $full, 1);
                        
                            // Category
                            $category = null;
                            if (isset($internals['modx_category'])) {
                                $category = trim($internals['modx_category']);
                            }
                            if (isset($internals['clpr_category'])) {
                                $category = trim($internals['clpr_category']);
                            }
                            if(!empty($category)) {
                                $category = $this->core->db->escape($category);
                                $rs_cat = $this->core->db->select('id', $this->core->getFullTableName('categories'), "category LIKE '$category'");
                                if ($this->core->db->getRecordCount($rs_cat)) {
                                    $flds['category'] = $this->core->db->getValue($rs_cat);
                                } else {
                                    $flds['category'] = $this->core->db->insert(array('category'=>$category), $this->core->getFullTableName('categories'));
                                }
                            }

                            switch ($el_category) {

                                case 'chunks':
                                    $tbl = $this->core->getFullTableName('site_htmlsnippets');
                                    $content_field = 'snippet';
                                    $name_field = 'name';
                                    $is_php = false;
                                    $disable_old = false;

                                    break;
                                
                                case 'snippets':
                                    if (isset($internals['properties'])) {
                                        $flds['properties'] = $this->core->db->escape($internals['properties']);
                                    }

                                    $tbl = $this->core->getFullTableName('site_snippets');
                                    $content_field = 'snippet';
                                    $name_field = 'name';
                                    $is_php = true;
                                    $disable_old = false; // <<<< If snippet map API implemented allow @internal @legacy_names for snippets

                                    break;
                                    
                                case 'plugins':
                                    if (isset($internals['properties'])) {
                                        $flds['properties'] = $this->core->db->escape($internals['properties']);
                                    }
                                    if (isset($internals['moduleguid'])) {
                                        $flds['moduleguid'] = $this->core->db->escape($internals['moduleguid']);
                                    }
                                    
                                    $tbl = $this->core->getFullTableName('site_plugins');
                                    $content_field = 'plugincode';
                                    $name_field = 'name';
                                    $is_php = true;
                                    $disable_old = true;
                                    
                                    // Delete old events
                                    $rs_plugins = $this->core->db->select('id', $tbl, "name='$name'");
                                    if ($this->core->db->getRecordCount($rs_plugins)) {
                                        $plugid = $this->core->db->getValue($rs_plugins);
                                        if (!$this->core->db->delete($this->core->getFullTableName('site_plugin_events'), "pluginid='$plugid'")) {
                                            $this->install_summary .= "<p class=\"error\">Error unsetting events for plugin $name</p>";
                                            return false;
                                        }
                                        $this->install_summary .= "<p>Unsetting events for plugin $name</p>";
                                    }
                                    
                                    break;
                                    
                                case 'modules':
                                    // guid, share_params
                                    if (isset($internals['properties'])) {
                                        $flds['properties'] = $this->core->db->escape($internals['properties']);
                                    }
                                    if (isset($internals['guid'])) {
                                        $flds['guid'] = $this->core->db->escape($internals['guid']);
                                    }
                                    if (isset($internals['shareparams'])) {
                                        $flds['enable_sharedparams'] = $this->core->db->escape($internals['shareparams']);
                                    }
                                    
                                    $tbl = $this->core->getFullTableName('site_modules');
                                    $content_field = 'modulecode';
                                    $name_field = 'name';
                                    $is_php = true;
                                    $disable_old = true;
                                    
                                    break;
                               
                               case 'templates':
                                    if (isset($internals['default_child_template'])) {
                                        $flds['default_child_template'] = $this->core->db->escape($internals['default_child_template']);
                                    }
                                    if (isset($internals['restrict_children'])) {
                                        $flds['restrict_children'] = $this->core->db->escape($internals['restrict_children']);
                                    }
                                    if (isset($internals['allowed_child_templates'])) {
                                        $flds['allowed_child_templates'] = $this->core->db->escape($internals['allowed_child_templates']);
                                    }
                               
                                    $tbl = $this->core->getFullTableName('site_templates');
                                    $content_field = 'content';
                                    $name_field = 'templatename';
                                    $is_php = false;
                                    $disable_old = false;
                                    break;
                               
                               case 'tvs':
                                    $flds['caption'] = $this->core->db->escape(isset($internals['caption']) ? $internals['caption'] : $name);
                                    
                                    if (isset($internals['input_type'])) {
                                        $flds['type'] = $this->core->db->escape($internals['input_type']);
                                    } else {
                                        $flds['type'] = 'text';
                                    }
                                    
                                    if (isset($internals['input_options'])) {
                                        $flds['elements'] = $this->core->db->escape($internals['input_options']);
                                    }
                                    if (isset($internals['input_default'])) {
                                        $flds['default_text'] = $this->core->db->escape($internals['input_default']);
                                    }
                                    if (isset($internals['output_widget'])) {
                                        $flds['display'] = $this->core->db->escape($internals['output_widget']);
                                    }
                                    if (isset($internals['output_widget_params'])) {
                                        $flds['display_params'] = $this->core->db->escape($internals['output_widget_params']);
                                    }

                                    $tbl = $this->core->getFullTableName('site_tmplvars');
                                    $content_field = null;
                                    $name_field = 'name';
                                    $is_php = false;
                                    $disable_old = false;
                                    
                                    break;
                            }

                            // Disable elements with legacy names
                            if ($disable_old && isset($internals['legacy_names'])) {
                                $this->disable_legacy_elements($type, $tbl, $name, $internals['legacy_names']);
                            }

                            if ($is_php) {
                                // Remove start and end PHP tags
                                $content = $this->prepare_for_eval($content);
                            }
                            
                            // Name and description fields   
                            $flds[$name_field] = $this->core->db->escape($name);
                            $flds['description'] = $this->core->db->escape('<strong>'.$full_version.'</strong> '.$desc);

                            // Put into db
                            $rs = $this->core->db->select('*', $tbl, "$name_field LIKE '$name'");
                            if (!$this->core->db->getRecordCount($rs) || $overwrite) {
                                if ($this->core->db->getRecordCount($rs)) {
                                    if (!$this->core->db->delete($tbl, "$name_field LIKE '$name'")) {
                                        $this->install_summary .= "<p class=\"error\">Error removing existing $el_category_singular $name</p>";
                                        return false;
                                    }
                                    $this->install_summary .= "<p>Removing existing $el_category_singular $name</p>";
                                }
                                if ($content_field) {
                                    $flds[$content_field] = $this->core->db->escape($content);
                                }
                                if (!$new_id = $this->core->db->insert($flds, $tbl)) {
                                    $this->install_summary .= "<p class=\"error\">Error installing new $el_category_singular $name</p>";
                                    return false;
                                }
                                $this->install_summary .= "<p>Installing new $el_category_singular $name</p>";
                            }
                            
                            switch($el_category) {
                                
                                case 'plugins':
                                    // New events
                                    if (isset($internals['events']) && sizeof($events = preg_split('/\s*,\s*/', $internals['events'], -1, PREG_SPLIT_NO_EMPTY))) {
                                        $tbl_se = $this->core->getFullTableName('site_plugin_events');
                                        $tbl_sen = $this->core->getFullTableName('system_eventnames');
                                        if (!$this->core->db->insert('(pluginid, evtid)' , $tbl_se, "$new_id AS pluginid, $tbl_sen.id as evtid", $tbl_sen, 'name IN (\''.implode('\',\'', $events).'\')')) {
                                            $this->install_summary .= "<p class=\"error\">Error setting events for plugin $name</p>";
                                            return false;
                                        }
                                        $this->install_summary .= "<p>Setting events for plugin $name</p>";
                                    }
                                    
                                    break;
                            }
                        }
                    }
                }
            }

            // empty cache
            require_once($this->core->config['base_path'].'manager/processors/cache_sync.class.processor.php');
            $sync = new synccache();
            $sync->setCachepath($this->core->config['base_path'].'assets/cache/');
            $sync->setReport(false);
            $sync->emptyCache();
            
            // Run optional install script
            if ($code = $this->package->read_file('install.php')) {
                $core = &$this->core; // For convienience in install script
                $retval = eval($this->prepare_for_eval($code));
                if ($retval === false) {
                    $this->error_msgs[] = 'Error running install.php script';
                } elseif (strtolower(substr($retval, 0, 5)) == 'error') {
                    $this->error_msgs[] = $retval;
                } else {
                    $this->install_summary .= $retval;
                }
            }

        } else {
            $this->error_msgs[] = 'Error opening package';
        }
    }
}

