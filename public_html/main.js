function chk_uploadplugin()
{
    // Get each field from the table, and check it for errors
    var field;
    var msg = "";
    
    // Error field must be blank
    document.getElementById("GEEKLOG_PUPLOAD_ERRFORM").innerHTML = msg;
    
  try
  {
     // Plugin Name
     field = document.getElementById("GEEKLOG_PLNAME").value;
    
     if ((field < 3) || (field > 100)) {
	 msg += "<br />"+LANG_PLUPLOAD_MSG0;
     }
     
     // Plugin Version
     field = document.getElementById("GEEKLOG_PLVERSION").value;
     
     if ((field < 1) || (field > 100)) {
         msg += "<br />"+LANG_PLUPLOAD_MSG1;
     }
     
     // Make sure at least one database is supported
     // So check MySQL first, if no, check MSSQL, if no, check POSTGRE, if no, error
     field = document.getElementById("GEEKLOG_PLMYSQL").value;
     
     if (field == "no") {
         // Check MSSQL
	 field = document.getElementById("GEEKLOG_PLMSSQL").value;
	 
	 if (field == "no") {
	     // Check POSTGRE
	     field = document.getElementById("GEEKLOG_PLPOSTGRE").value;
	     
	     if (field == "no") {
	         // Error
		 msg += "<br />"+LANG_PLUPLOAD_MSG2;
	     }
	 }
      }
  
      // Get textarea short discussion
      field = document.getElementById("GEEKLOG_SHRTDES").value;
      
      if ((field > 200) || (field < 3)) {
          msg += "<br />"+LANG_PLUPLOAD_MSG3;
      }

     // Done now, set the error field
     document.getElementById("GEEKLOG_PUPLOAD_ERRFORM").innerHTML = msg;

     if (msg != "") {
	 var s = window.location.href;
	 window.location.href = s.replace("#GEEKLOG_PUPLOAD_ERRFORM","") + "#" + "GEEKLOG_PUPLOAD_ERRFORM";
         return false;  
     }
    
   }
   catch(error)
   {
       alert(LANG_PLUPLOAD_MSG4+": #"+error);
       return false;
   }

    return true;
};