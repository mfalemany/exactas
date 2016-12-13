<?php
 /*
     Example10 : A 3D exploded pie graph
 */

 // Standard inclusions   
 include("pChart/pData.class");
 include("pChart/pChart.class");

 // Dataset definition 
 $DataSet = new pData;
 $DataSet->AddPoint(array(50,1,24,25),"Serie1");
 $DataSet->AddPoint(array("Mayor","February","March","April"),"Serie2");
 $DataSet->AddAllSeries();
 $DataSet->SetAbsciseLabelSerie("Serie2");

 // Initialise the graph
 $Test = new pChart(420,250);
 $Test->drawFilledRoundedRectangle(7,7,413,243,5,200,200,200);
 $Test->drawRoundedRectangle(5,5,415,245,5,230,230,230);
 $Test->createColorGradientPalette(195,204,56,223,110,41,5);

 // Draw the pie chart
 $Test->setFontProperties("Fonts/tahoma.ttf",8);
 $Test->AntialiasQuality = 0;
 $Test->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),180,130,110,PIE_PERCENTAGE_LABEL,FALSE,50,20,5);
 $Test->drawPieLegend(330,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);

 // Write the title
 $Test->setFontProperties("Fonts/MankSans.ttf",11);
 $Test->drawTitle(10,22,"Resultado de Encuestas",0,0,0);

 $Test->Render("example10.png");
?>