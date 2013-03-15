<!doctype html>
<html>
    <head>
        <title>[+lang.RM_update_title+]</title>

        <link rel="stylesheet" href="media/style/common/style.css" />
		[+style.css+]
		[+manager.css+]
	   
	    [+jquery+]
		[+jquery.ui+]
		[+jquery.timepicker+]

        <script>
	        function reset() {
	           $('#backform').submit();
	        }
        </script>
        <style> 
            .topdiv {
                border:0;
            } 
            .subdiv {
                border:0;
            } 
            ul, li {
                list-style:none;
            } 
        </style>
        <script type="text/javascript">
        	if(parent.tree !== undefined)
        		parent.tree.updateTree();
        </script>
    </head>
    <body>
        <h1>[+lang.RM_module_title+]</h1>
        <div id="actions">
		    <ul class="actionButtons">
		           <li id="Button1"><a href="#" onclick="document.location.href='index.php?a=106';"><img src="media/style[+theme+]/images/icons/stop.png" align="absmiddle"> [+lang.RM_close+]</a></li>
		           <li id="Button4"><a href="#" onclick="reset();"><img src="media/style[+theme+]/images/icons/cancel.png" align="absmiddle"> [+lang.RM_cancel+]</a></li>
		    </ul>
	    </div>
	   
	    <div class="sectionHeader">[+lang.RM_update_title+]</div> 
	    <div class="sectionBody"> 
	       <p>[+update.message+]</p>
		   <form id="backform" method="post" style="display: none;">
		      <input type="submit" name="back" value="[+lang.RM_process_back+]" />
		   </form>
	    </div>
    </body>
</html>