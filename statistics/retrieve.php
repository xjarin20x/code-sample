<?php

require_once("../includes/header.php");
$prepath = checksubdomain(dirname(__FILE__), $conn, $purifier);

$content = "";
$table_list = array(
    
	1 => array("Estimated production, area harvested and yield per hectare (PAY)"),
	2 => array("Monthly relative distribution (%) of palay production"),
	3 => array("Monthly relative distribution (%) of area harvested"),
	4 => array("Relative distribution (%) of palay production utilization and disposition of farm households"),
	5 => array("Production and use estimates"),
	6 => array("Estimated physical area, effective area and cropping intensity"),
	7 => array("Relative distribution (%) of farms reporting - by type of crop establishment, by semester"),
	8 => array("Relative distribution (%) of farms reporting - by seed class usage, by semester"),
	9 => array("Estimated per hectare seed use - by seed class and type of crop establishment"),
	13 => array("Estimated production losses, value, area affected and damaged - by cause"),
	14 => array("Annual and semestral average farmgate, wholesale and retail prices, special palay/rice"),
	15 => array("Annual and semestral average farmgate, wholesale and retail prices, ordinary palay/rice"),
	16 => array("Quantity and value of imports, by country of origin"),
	17 => array("Quantity and value of exports, by country of destination")
    
);

// Start $_GET['table'] if else statement
if(isset($_GET['table'])) {
    
	$table = disinfect_var($_GET['table']);
    
    // Start array_key_exists($table, $table_list) if else statement
	if (array_key_exists($table, $table_list)) {
        
?>
        <div id="stat-content" class="page col-lg-10 mx-auto">
            <div class="page-header col-lg-8 mx-auto">
                <h2 id="stat-title"><?php echo $table_list[$table][0]; ?></h2>
                <p><?php echo $GLOBALS['tag']['description']; ?></p>
            </div>
            <div class="page-body col-lg-8 mx-auto">
                <div id="stat-input">
                    <ul class="nav nav-tabs" id="stat-options" role="tablist">
                         <li class="nav-item" role="presentation"><a class="nav-link active" id="data-tab" data-toggle="tab" href="#data" role="tab" aria-controls="data" aria-selected="true">Add/Remove Data</a></li>
<?php
        
        // Start 1st if ($table != 16 && $table != 17)
        if($table != 16 && $table != 17){
        
?>
                        <li class="nav-item" role="presentation"><a class="nav-link" id="profile-tab" data-toggle="tab" href="#branch" role="tab" aria-controls="profile" aria-selected="false">For Branch Stations</a></li>
<?php
 
        }
        // End 1st if ($table != 16 || $table != 17)
        
?>
                </ul>
                <div class="tab-content" id="stat-optionsContent">
                        <div class="tab-pane fade show active" id="data" role="tabpanel" aria-labelledby="data-tab">
<?php
        
        // Start 2nd if else ($table == 16 || $table == 17)
        if($table == 16 || $table == 17){
        
?>
                        <div class="alert alert-info" role="alert">All fields marked with an asterisk (*) are required.</div>
                        <form id="build" method="post" action="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/statistics/stat<?php echo $table; ?>_function.php">
                            <div class="form-group">
                                <label for="regions">Countries*</label>
                                <select id="countries" class="form-control multi-select" name="countries[]" multiple="multiple" tabindex="1" title="Not specified" data-live-search="true" data-size="10" data-selected-text-format="count > 3" data-actions-box="true">
<?php
            // Start countries fetch statement
            if ($sstmt = $conn->prepare("SELECT stable_name FROM cms_stables WHERE stable_id = ?")) {
                
                $sstmt->bind_param("s", $table);
                $sstmt->execute();
                $sstmt->store_result();
                $sstmt->bind_result($tname);
                
                while($sstmt->fetch()){
                    
                    if ($rstmt = $conn->prepare("SELECT c.iso_code, c.country_name FROM legend_country c JOIN (SELECT DISTINCT iso_code FROM " . $tname . ")l WHERE c.iso_code = l.iso_code ORDER BY (c.iso_code) ASC")) {
                        
                        $rstmt->execute();
                        $rstmt->bind_result($index, $item);
                        while($rstmt->fetch()){
                        
                          echo "    <option value='" . $index . "'>" . $item . "</option>\n";
                        
                        }
                        $rstmt->close();				
                        
                    }
                }
                $rstmt->close();
            }
            // End sstmt statement
?>
                                </select>
                            </div>
                            <input id="countries-options" class="btn btn-default options" type="button" value="OK"><br/>
<?php
        
        }
        else {

?>
                        <div class="alert alert-info" role="alert">You may fill either the <strong>Regions</strong>, <strong>Provinces</strong>, or <strong>Cities</strong> field, but at least 1 of the 3 must be selected.  Other fields marked with an asterisk (*) are required. </div>
				        <form id="build" method="post" action="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/statistics/stat<?php echo $table; ?>_function.php">
                            <div class="form-group">
                                <label for="regions">Regions</label>
                                <select id="regions" class="form-control multi-select" name="regions[]" multiple="multiple" tabindex="1" title="Not specified" data-live-search="true" data-size="10" data-selected-text-format="count > 3" data-actions-box="true">
<?php
            // Start regions fetch statement
            if ($sstmt = $conn->prepare("SELECT stable_name FROM cms_stables WHERE stable_id = ?")) {
                
                $sstmt->bind_param("s", $table);
                $sstmt->execute();
                $sstmt->store_result();
                $sstmt->bind_result($tname);
                
                while($sstmt->fetch()){
                    if ($rstmt = $conn->prepare("SELECT r.region_id, r.region_name FROM legend_region r JOIN (SELECT DISTINCT location_code FROM " . $tname . " WHERE location_type = 1)l WHERE r.region_id = l.location_code ORDER BY (r.region_sort) ASC")) {
                        
                        $rstmt->execute();
                        $rstmt->bind_result($index, $item);
                        while($rstmt->fetch()){
                            
                          echo "    <option value='" . $index . "'>" . $item . "</option>\n";
                        
                        }
                        $rstmt->close();				
                        
                    }
                }
                
                $sstmt->close();
                
            }
              
?>
                                </select>
                            </div>
                            <input id="regions-options" class="btn btn-default options" type="button" value="OK"><br/>
                            <div class="form-group">
                                <label for="provinces">Provinces</label>
                                <select id="provinces" class="form-control multi-select" name="provinces[]" multiple="multiple" tabindex="1" title="Not specified" data-live-search="true" data-size="10" data-selected-text-format="count > 3" data-actions-box="true">
<?php

            echo "                  <option value='999'>PHILIPPINES</option>\n";
            // Start provinces fetch statement
            if ($rstmt = $conn->prepare("SELECT r.region_name, p.province_id, p.province_name FROM legend_province p JOIN legend_region r, (SELECT DISTINCT location_code, location_type FROM " . $tname . " WHERE location_type = 2)l WHERE r.region_id = p.region AND p.province_id = l.location_code ORDER BY r.region_sort ASC, p.province_name ASC")) {
                
                $rstmt->execute();
                $rstmt->store_result();
                $rstmt->bind_result($group, $index, $item);
                $group_temp = "";


                while($rstmt->fetch()){
                    
                    if (strcasecmp($group_temp, $group) != 0 && $group_temp != ""){
                        
                        echo "      </optgroup>\n";

                    }
                    if (strcasecmp($group_temp, $group) != 0){
                        
                        echo "	    <optgroup label='". $group ."'>\n";
                        $group_temp = $group;
                        
                    }
                    
                    echo "              <option value='" . $index . "'>" . $item . "</option>\n";
                }

                $rstmt->close();
                echo "              </optgroup>\n";
                
            }
                  
?>
                                </select>
                            </div>
                            <input id="provinces-options" class="btn btn-default options" type="button" value="OK"><br/>
                            <div class="form-group">
                                <label for="cities">Cities</label>
						        <select id="cities" class="form-control multi-select" name="cities[]" multiple="multiple" tabindex="1" title="Not specified" data-live-search="true" data-size="10" data-selected-text-format="count > 3" data-actions-box="true">
<?php
              
            if ($rstmt = $conn->prepare("SELECT p.province_name, c.city_id, c.city_name FROM legend_city c JOIN legend_province p, (SELECT DISTINCT location_code, location_type FROM " . $tname . " WHERE location_type = 3)l WHERE c.province_id = p.province_id AND c.city_id = l.location_code ORDER BY p.province_name ASC, c.city_name ASC")) {
                $rstmt->execute();
                $rstmt->store_result();
                $rstmt->bind_result($group, $index, $item);
                $group_temp = "";
                
                while($rstmt->fetch()){
                    
                    if (strcasecmp($group_temp, $group) != 0 && $group_temp != ""){
                        
                        echo "      </optgroup>\n";
                        
                    }
                    if (strcasecmp($group_temp, $group) != 0){
                        
                        echo "	    <optgroup label='". $group ."'>\n";
                        $group_temp = $group;
                        
                    }
                    
                    echo "              <option value='" . $index . "'>" . $item . "</option>\n";
                    
                    
                }
                
                $rstmt->close();				
                echo "              </optgroup>\n";
                
            }
              
?>
                                </select>
                            </div>
                            <input id="cities-options" class="btn btn-default options" type="button" value="OK"><br/>                                    
<?php
            
        } // End 2nd if else ($table == 16 || $table == 17)
        
        if ($stmt = $conn->prepare("SELECT DISTINCT j.scategory_id, j.scategory FROM (SELECT f.scategory_id, c.scategory FROM cms_stables_scategories_sitems f JOIN cms_stables t, cms_scategories c, cms_sitems i WHERE f.stable_id = t.stable_id AND f.scategory_id = c.scategory_id AND f.sitem_id = i.sitem_id AND f.stable_id = ?)j")) {
            
            $stmt->bind_param("s", $table);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($category, $heading);
            
            while($stmt->fetch()){
   
                echo '      <div class="form-group">'; echo "\n";
                echo '          <label for="'. str_replace(' ', '_', strtolower($heading)) .'">'. $heading .'*</label>'; echo "\n";
                echo '          <select id="'. str_replace(' ', '_', strtolower($heading)) .'" class="form-control multi-select dynamic-select" name="'. str_replace(' ', '_', strtolower($heading)) .'[]" multiple="multiple" tabindex="1" title="Not specified" data-live-search="true" data-size="10" data-selected-text-format="count > 1" data-actions-box="true">'; echo "\n";
                
                if ($substmt = $conn->prepare("SELECT i.sitem_value, i.sitem FROM cms_stables_scategories_sitems f JOIN cms_stables t, cms_scategories c, cms_sitems i WHERE f.stable_id = t.stable_id AND f.scategory_id = c.scategory_id AND f.sitem_id = i.sitem_id AND f.stable_id = ? AND f.scategory_id = ? ORDER BY (i.sitem_value) ASC")) {
                    
                    $substmt->bind_param("ss", $table, $category);
                    $substmt->execute();
                    $substmt->bind_result($index, $item);
                    
                    while($substmt->fetch()){
                    
                        echo "	    <option value='" . $index . "'>" . $item . "</option>\n";
                    
                    }
                    
                    $substmt->close();			
                    
                }
                
                echo '          </select>'; echo "\n";
                echo '      </div>'; echo "\n";
                echo '      <input id="'. str_replace(' ', '_', strtolower($heading)) .'-options" class="btn btn-default options" type="button" value="OK"><br/>';
                
            }
            
            $stmt->close();			
        }
               
?>
                            <div class="form-group">
                                <label for="years">Years*</label>
						        <select id="years" class="form-control multi-select" name="years[]" multiple="multiple" tabindex="1" title="Not specified" data-live-search="true" data-size="10" data-selected-text-format="count > 3" data-actions-box="true">
<?php
        
        if ($ystmt = $conn->prepare("SELECT DISTINCT year FROM " . $tname)) {
            
            $ystmt->execute();
            $ystmt->bind_result($year);
            while($ystmt->fetch()){
                echo "	    	    <option value='" . $year . "'>" . $year . "</option>\n";
            }
            $ystmt->close();				
            
        }
        
?>
						        </select>
					       </div>
                           <input id="years-options" class="btn btn-default options" type="button" value="OK"><br/>
					       <input id="process" class="btn btn-primary" type="submit" value="Continue" disabled>
                        </form>
                    </div>
<?php
        
        // Start 2nd if ($table != 16 && $table != 17)
        if($table != 16 && $table != 17){
        
?>
                   <div class="tab-pane fade" id="branch" role="tabpanel" aria-labelledby="branch-tab">
                          <div class="alert alert-info" role="alert">You can easily select all provinces under your branch station's jurisdiction by choosing your station in the <strong>PhilRice Branch Stations</strong> field.</div>
                          <div class="alert alert-info" role="alert">Asterisk (*) indicates required field.</div>
                            <form id="build" method="post" action="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/statistics/stat<?php echo $table; ?>_function.php">
                                <div class="form-group">
                                    <label for="provinces-st">PhilRice Branch Stations*</label>
                                    <select id="provinces-st" class="form-control multi-select" name="provinces[]" multiple="multiple" tabindex="1" title="Not specified" data-live-search="true" data-size="10" data-selected-text-format="count > 3" data-actions-box="true">
<?php
            // Start branch stations fetch statement
            if ($rstmt = $conn->prepare("SELECT s.station_name, p.province_id, p.province_name FROM legend_province p JOIN legend_station s, (SELECT DISTINCT location_code, location_type FROM " . $tname . " WHERE location_type = 2)l WHERE s.station_id = p.station AND p.province_id = l.location_code ORDER BY s.station_id ASC, p.province_name ASC")) {
                
                $rstmt->execute();
                $rstmt->store_result();
                $rstmt->bind_result($group, $index, $item);
                $group_temp = "";


                while($rstmt->fetch()){
                    
                    if (strcasecmp($group_temp, $group) != 0 && $group_temp != ""){
                        
                        echo "          </optgroup>\n";

                    }
                    if (strcasecmp($group_temp, $group) != 0){
                        
                        echo "	        <optgroup label='". $group ."'>\n";
                        $group_temp = $group;
                        
                    }
                    
                    echo "                  <option value='" . $index . "'>" . $item . "</option>\n";
                }

                $rstmt->close();
                echo "                  </optgroup>\n";
                
            }
                  
?>
                                </select>
                            </div>
                            <input id="provinces-st-options" class="btn btn-default options" type="button" value="OK"><br/>
<?php
            
        if ($stmt = $conn->prepare("SELECT DISTINCT j.scategory_id, j.scategory FROM (SELECT f.scategory_id, c.scategory FROM cms_stables_scategories_sitems f JOIN cms_stables t, cms_scategories c, cms_sitems i WHERE f.stable_id = t.stable_id AND f.scategory_id = c.scategory_id AND f.sitem_id = i.sitem_id AND f.stable_id = ?)j")) {
            
            $stmt->bind_param("s", $table);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($category, $heading);
            
            while($stmt->fetch()){
   
                echo '      <div class="form-group">'; echo "\n";
                echo '          <label for="'. str_replace(' ', '_', strtolower($heading)) .'-st">'. $heading .'*</label>'; echo "\n";
                echo '          <select id="'. str_replace(' ', '_', strtolower($heading)) .'-st" class="form-control multi-select dynamic-select-st" name="'. str_replace(' ', '_', strtolower($heading)) .'[]" multiple="multiple" tabindex="1" title="Not specified" data-live-search="true" data-size="10" data-selected-text-format="count > 1" data-actions-box="true">'; echo "\n";
                
                if ($substmt = $conn->prepare("SELECT i.sitem_value, i.sitem FROM cms_stables_scategories_sitems f JOIN cms_stables t, cms_scategories c, cms_sitems i WHERE f.stable_id = t.stable_id AND f.scategory_id = c.scategory_id AND f.sitem_id = i.sitem_id AND f.stable_id = ? AND f.scategory_id = ? ORDER BY (i.sitem_value) ASC")) {
                    
                    $substmt->bind_param("ss", $table, $category);
                    $substmt->execute();
                    $substmt->bind_result($index, $item);
                    
                    while($substmt->fetch()){
                    
                        echo "	    <option value='" . $index . "'>" . $item . "</option>\n";
                    
                    }
                    
                    $substmt->close();			
                    
                }
                
                echo '          </select>'; echo "\n";
                echo '      </div>'; echo "\n";
                echo '      <input id="'. str_replace(' ', '_', strtolower($heading)) .'-st-options" class="btn btn-default options" type="button" value="OK"><br/>';
                
            }
            
            $stmt->close();			
        }
               
?>
                            <div class="form-group">
                                <label for="years-st">Years*</label>
						        <select id="years-st" class="form-control multi-select" name="years[]" multiple="multiple" tabindex="1" title="Not specified" data-live-search="true" data-size="10" data-selected-text-format="count > 3" data-actions-box="true">
<?php
        
        if ($ystmt = $conn->prepare("SELECT DISTINCT year FROM " . $tname)) {
            
            $ystmt->execute();
            $ystmt->bind_result($year);
            while($ystmt->fetch()){
                echo "	    	    <option value='" . $year . "'>" . $year . "</option>\n";
            }
            $ystmt->close();				
            
        }
        
?>
						        </select>
					       </div> 
                           <input id="years-st-options" class="btn btn-default options" type="button" value="OK"><br/>
					       <input id="process-st" class="btn btn-primary" type="submit" value="Continue" disabled>
                        </form>
                    </div>
<?php
        
        }
        
?>
                    </div>
                </div>
                <div id="stat-output">
                    <div class="loading d-flex justify-content-center">
                      <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Building custom data tables...</span>
                      </div>
                      <p class="text-muted"> Building custom data tables...</p>
                    </div>
                </div>
            </div>
        </div>
            
<?php
        
	}  
    else {
        
        echo    '	<div class="page col-lg-8 mx-auto">
	                       <div class="page-header">
                              <h1>Page Not Found</h1>
		                      <p>Try using search to find what you are looking for.</p>
                            </div>
                            <div class="page-body">
                                <p class="extend">The page you requested cannot be found.</p><br/>
                            </div>
                        </div>';
        
    }
    // End array_key_exists($table, $table_list) if else statement
    
}
else {
    
	header("HTTP/1.1 401 Unauthorized");
    header("Location: index.php");
    
}
// End $_GET['table'] if else statement

require_once("../includes/footer.php");
?>