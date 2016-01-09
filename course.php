<!DOCTYPE html>
<html>
<head>
	<title>Aggregation plugin for Open edX</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>

<?php
$start = microtime(true);
mb_internal_encoding("UTF-8");
$m = new MongoClient();
$db = $m->selectDB("edxapp");

$subject = array();$s=0;
$property = array();
$object = array();$lang="@en .";



$collection_active = $db->selectCollection("modulestore.active_versions");
$course_id = $collection_active->find(array(),array('_id' => 'false','versions.published-branch' => 'true'));
$array = iterator_to_array($course_id);

foreach($array as $tmp){
	$id=$tmp["versions"]["published-branch"];
	
	
		
	$collection_struct = $db->selectCollection("modulestore.structures");
	$course_name = $collection_struct->find(array('_id' => $id, 'blocks.block_id' => "course"),array('blocks.definition' => 'true','blocks.block_id' => 'true','blocks.block_type' => 'true','blocks.fields.display_name' => 'true','blocks.fields.children' => 'true','_id' => 'true'));
	$names = iterator_to_array($course_name);
	foreach($names as $dispname){
		//ищем блок где имя курса
		
		//statistics
		$sections=0;
		$subsections=0;
		$units=0;
		$htmllec=0;
		
		for($i=0;$i<sizeof($dispname["blocks"]);$i++){
			
			if ($dispname["blocks"][$i]["block_type"]=="course") {

				$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#ObjectId('".$id."')>";
				$subject[$s+1]=$subject[$s];
				$subject[$s+2]=$subject[$s];
				$subject[$s+3]=$subject[$s];
				$property[$s]="<http://www.w3.org/1999/02/22-rdf-syntax-ns#type>";
				$property[$s+1]=$property[$s];
				$property[$s+2]="<http://www.semanticweb.org/EdxOntology/Main#name>";
				$property[$s+3]="<http://www.w3.org/2000/01/rdf-schema#label>";
				$object[$s]="<http://www.w3.org/2002/07/owl#NamedIndividual> .";
				$object[$s+1]="<http://www.semanticweb.org/EdxOntology/Main#Course> .";
				$object[$s+2]='"'.$dispname["blocks"][$i]["fields"]["display_name"].'"'.$lang;$s+=3;
				
				//course name
				echo "<b>".$dispname["blocks"][$i]["fields"]["display_name"]."</b> -> statistics:<br>";
				}}
				$object[$s+3]=$object[$s+2];
				
				//ищем блоки с секциями
		for($i=0;$i<sizeof($dispname["blocks"]);$i++){	//i - текущая секция
		
			if ($dispname["blocks"][$i]["block_type"]=="chapter") { //проходим по всем i блокам в поисках chapter
				
				$sections++;
				//нашли секцию с именем
				//echo $dispname["blocks"][$i]["fields"]["display_name"]." ";
				$sectionid=$dispname["blocks"][$i]["block_id"];
				$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#ObjectId('".$id."')>";
				$property[$s]="<http://www.semanticweb.org/EdxOntology/Main#hasSections>";
				$object[$s]="<http://www.semanticweb.org/EdxOntology/Main#Section_".$sectionid."> .";$s++;
				
				$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#Section_".$sectionid.">";
				$property[$s]="<http://www.w3.org/1999/02/22-rdf-syntax-ns#type>";
				$object[$s]="<http://www.w3.org/2002/07/owl#NamedIndividual> .";$s++;
				
				$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#Section_".$sectionid.">";
				$property[$s]="<http://www.w3.org/1999/02/22-rdf-syntax-ns#type>";
				$object[$s]="<http://www.semanticweb.org/EdxOntology/Main#Sections> .";$s++;

				$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#Section_".$sectionid.">";
				$property[$s]="<http://www.semanticweb.org/EdxOntology/Main#name>";
				$object[$s]='"'.str_replace("\"","&quot;",$dispname["blocks"][$i]["fields"]["display_name"]).'"@en .';$s++;

				$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#Section_".$sectionid.">";
				$property[$s]="<http://www.w3.org/2000/01/rdf-schema#label>";
				$object[$s]='"'.str_replace("\"","&quot;",$dispname["blocks"][$i]["fields"]["display_name"]).'"@en .';$s++;

								
				$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#Section_".$sectionid.">";
				$property[$s]="<http://www.semanticweb.org/EdxOntology/Main#belongsToCourse>";
				$object[$s]="<http://www.semanticweb.org/EdxOntology/Main#ObjectId('".$id."')> .";$s++;
				 
					
				for($j=0;$j<sizeof($dispname["blocks"][$i]["fields"]["children"]);$j++){ //проходим по всем j-children секции в поисках id всех подсекций
					//id по которому искать подсекции
					//echo $dispname["blocks"][$i]["fields"]["children"][$j]["1"]." | ";
					
					for($k=0;$k<sizeof($dispname["blocks"]);$k++){ //опять проходим по всем блокам в поисках совпадения с id подсекции
						if($dispname["blocks"][$k]["block_id"]==$dispname["blocks"][$i]["fields"]["children"][$j]["1"]){ 
							$subsectionid=$dispname["blocks"][$k]["block_id"];
							//нашли имена подсекций
							$subsections++;
							//echo $dispname["blocks"][$k]["fields"]["display_name"]." ";
								
								$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#Section_".$sectionid.">";
								$property[$s]="<http://www.semanticweb.org/EdxOntology/Main#hasSubsections>";
								$object[$s]="<http://www.semanticweb.org/EdxOntology/Main#Subsection_".$subsectionid."> .";$s++;
								
								$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#Subsection_".$subsectionid.">";
								$property[$s]="<http://www.w3.org/1999/02/22-rdf-syntax-ns#type>";
								$object[$s]="<http://www.w3.org/2002/07/owl#NamedIndividual> .";$s++;
								
								$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#Subsection_".$subsectionid.">";
								$property[$s]="<http://www.w3.org/1999/02/22-rdf-syntax-ns#type>";
								$object[$s]="<http://www.semanticweb.org/EdxOntology/Main#Subsections> .";$s++;
								
								$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#Subsection_".$subsectionid.">";
								$property[$s]="<http://www.semanticweb.org/EdxOntology/Main#name>";
								$object[$s]='"'.str_replace("\"","&quot;",$dispname["blocks"][$k]["fields"]["display_name"]).'"@en .';$s++;
								
								$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#Subsection_".$subsectionid.">";
								$property[$s]="<http://www.w3.org/2000/01/rdf-schema#label>";
								$object[$s]='"'.str_replace("\"","&quot;",$dispname["blocks"][$k]["fields"]["display_name"]).'"@en .';$s++;

								$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#Subsection_".$subsectionid.">";
								$property[$s]="<http://www.semanticweb.org/EdxOntology/Main#belongsToSection>";
								$object[$s]="<http://www.semanticweb.org/EdxOntology/Main#Section_".$sectionid."> . ";$s++;
								  
								//id всех юнитов
								for($u=0;$u<sizeof($dispname["blocks"][$k]["fields"]["children"]);$u++){
								
									//id по которому искать юниты
									//echo $dispname["blocks"][$k]["fields"]["children"][$u]["1"]." | ";
																
								for($r=0;$r<sizeof($dispname["blocks"]);$r++){
								if($dispname["blocks"][$r]["block_id"]==$dispname["blocks"][$k]["fields"]["children"][$u]["1"]){
									$units++;
									$unitid=$dispname["blocks"][$k]["fields"]["children"][$u]["1"];
									
									$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#Subsection_".$subsectionid.">";
									$property[$s]="<http://www.semanticweb.org/EdxOntology/Main#hasUnits>";
									$object[$s]="<http://www.semanticweb.org/EdxOntology/Main#Unit_".$unitid."> .";$s++;
									
									$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#Unit_".$unitid.">";
									$property[$s]="<http://www.w3.org/1999/02/22-rdf-syntax-ns#type>";
									$object[$s]="<http://www.w3.org/2002/07/owl#NamedIndividual> .";$s++;
									
									$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#Unit_".$unitid.">";
									$property[$s]="<http://www.w3.org/1999/02/22-rdf-syntax-ns#type>";
									$object[$s]="<http://www.semanticweb.org/EdxOntology/Main#Units> .";$s++;
									
									$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#Unit_".$unitid.">";
									$property[$s]="<http://www.semanticweb.org/EdxOntology/Main#name>";
									$object[$s]='"'.str_replace("\"","&quot;",$dispname["blocks"][$r]["fields"]["display_name"]).'"@en .';$s++;
									
									$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#Unit_".$unitid.">";
									$property[$s]="<http://www.w3.org/2000/01/rdf-schema#label>";
									$object[$s]='"'.str_replace("\"","&quot;",$dispname["blocks"][$r]["fields"]["display_name"]).'"@en .';$s++;

									$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#Unit_".$unitid.">";
									$property[$s]="<http://www.semanticweb.org/EdxOntology/Main#belongsToSubsection>";
									$object[$s]="<http://www.semanticweb.org/EdxOntology/Main#Subsection_".$subsectionid."> .";$s++;
									
									//имя юнита
									//echo $dispname["blocks"][$r]["fields"]["display_name"]." | ";
									//id 
									//print_r($dispname["blocks"][$r]["fields"]["children"]);
									
									for($we=0;$we<sizeof($dispname["blocks"][$r]["fields"]["children"]);$we++){ //по всем we-детям чтобы найти все id
									
									//id-содержимого
									//echo $dispname["blocks"][$r]["fields"]["children"][$we]["1"];
									
									for($m=0;$m<sizeof($dispname["blocks"]);$m++){ //опять по всем блокам ищем id содержимого
										if($dispname["blocks"][$m]["block_id"]==$dispname["blocks"][$r]["fields"]["children"][$we]["1"]){
											$objid=$dispname["blocks"][$m]["definition"];
											$htmlid=$objid;
											//objectId содержимого, дальше идем в другую коллекцию за данными
											//echo($dispname["blocks"][$m]["definition"]);
											$collection_def = $db->selectCollection("modulestore.definitions");
											$data = $collection_def->find(array('_id' => new MongoId($objid)),array('_id' => 'false','fields.data' => 'true'));
											$source = iterator_to_array($data);
												foreach($source as $html){
													
												$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#Unit_".$unitid.">";
												$property[$s]="<http://www.semanticweb.org/EdxOntology/Main#hasBlock>";
												$object[$s]="<http://www.semanticweb.org/EdxOntology/Main#HTML_".$htmlid."> .";$s++;
													
												$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#HTML_".$htmlid.">";
												$property[$s]="<http://www.w3.org/1999/02/22-rdf-syntax-ns#type>";
												$object[$s]="<http://www.w3.org/2002/07/owl#NamedIndividual> .";$s++;
												
												$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#HTML_".$htmlid.">";
												$property[$s]="<http://www.w3.org/1999/02/22-rdf-syntax-ns#type>";
												$object[$s]="<http://www.semanticweb.org/EdxOntology/Main#HTML> .";$s++;
												
												$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#HTML_".$htmlid.">";
												$property[$s]="<http://www.semanticweb.org/EdxOntology/Main#name>";
												$object[$s]='"'.str_replace("\"","&quot;",$dispname["blocks"][$r]["fields"]["display_name"]).'"@en .';$s++;
												
												$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#HTML_".$htmlid.">";
												$property[$s]="<http://www.w3.org/2000/01/rdf-schema#label>";
												$object[$s]='"'.str_replace("\"","&quot;",$dispname["blocks"][$r]["fields"]["display_name"]).'"@en .';$s++;
																								
												$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#HTML_".$htmlid.">";
												$property[$s]="<http://www.semanticweb.org/EdxOntology/Main#belongsToUnit>";
												$object[$s]="<http://www.semanticweb.org/EdxOntology/Main#Unit_".$unitid.">";$s++;

													//данные html
													$ht=str_replace("\n", "", str_replace("\n", "", strip_tags(str_replace("\"","&quot;",$html["fields"]["data"]))));
													$ht=htmlspecialchars($ht,ENT_XML1);
													$ht = preg_replace('| +|', ' ', $ht);
													$ht = preg_replace('|\s+|', ' ', $ht);
													$ht=str_replace("\\", "", $ht);
													//$ht=preg_replace('/[^a-zA-z0-9]+?/','',$ht);
													//echo $ht;
													//echo "<br>";
												$subject[$s]="<http://www.semanticweb.org/EdxOntology/Main#HTML_".$htmlid.">";
												$property[$s]="<http://www.semanticweb.org/EdxOntology/Main#content>";
												
												$object[$s]='"'.$ht.'"@en .';$s++;
												$htmllec++;
												}	
										}
									}}
									
								}
								
							}}
						}
					}
				}
			}
		
		}
	}
echo "| Sections = ".$sections." | "."Subsections = ".$subsections." | "."Units = ".$units." | "."HTML Lectures = ".$htmllec." |<br><br>";	
}


//вывод в файл
$ntriples=array('subject'=>$subject,'property'=>$property,'object'=>$object);
//print_r($ntriples);
file_put_contents("ntriples1.nt", "");

for($s=0;$s<sizeof($subject);$s++){
$string=$ntriples["subject"][$s]." ".$ntriples["property"][$s]." ".$ntriples["object"][$s]."\r\n";
$f = fopen("ntriples1.nt", "a");
fwrite($f, $string);
fclose($f);}
echo "--------------------------------------------<br>";
echo "Total statements = ".sizeof($subject);
echo "<br><br>";
$time = microtime(true) - $start;

$fileopen=fopen("time.txt", "a+");
$write=$time."\r\n";
fwrite($fileopen,$write);
fclose($fileopen);


printf('Script time %.4F seconds.', $time);
?>
</body>
</html>
