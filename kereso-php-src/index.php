<html>
    <head>
        <title>Kereso</title>
    <body>
        <div style="font-size: 32px; font-weigth: bold">
            <span style="color: #ff0000">K</span>
        <span style="color: #00ff00">E</span>
        <span style="color: #0000ff">R</span>
        <span style="color: #00ff00">E</span>
        <span style="color: #FFA600">S</span>
        <span style="color: #0000ff">Õ</span>
        </div>
        <?php
            $query = "";
            $ekezetes = array('á', 'é', 'í', 'ó', 'ö', 'õ', 'ú', 'ü', 'û');
            $angol = array('a', 'e', 'i', 'o', 'o', 'o', 'u', 'u', 'u');
            
            if (isset($_GET["q"]) && $_GET["q"] != "") {
                $query = $_GET["q"];
                
                //Lecsereljuk az ekezetes karaktereket angol megfeleloikre
                $query = str_replace($ekezetes, $angol, $query);
                
                //Ellenorizzuk, hogy csak a abc karaktereket adtak -e meg.
                if(!preg_match("/^[A-Za-z]+$/", $query)){
                    $query = "";
                    echo "<div style='color:#cc0000'>Hiba! Érvénytelen karaktert adtál meg!</div>";
                }
            }
        ?>
        <form action="" method="GET">
            <input type="text" name="q" value="<?php echo $query; ?>">
            <input type="submit" value="Keres">
        </form>
        <br />

        <?php
        

        if ($query != "") {


            $results = array();
            
            //beolvassuk a szavakat tartalmazo fajlt, es soronkent tombbe rakjuk
            $filename = "index_depth.txt";
            $handle = fopen($filename, "r");
            $contents = fread($handle, filesize($filename));
            fclose($handle);
            $rows = explode("\n", $contents);


            foreach ($rows as $row) {
                $data = explode(" ", $row);
                if($data[0] == $query) { //ha a keresett szo van ebben a sorban
                    for ($i = 1; $i < count($data); $i++) {
                        $results[] = $data[$i]; //akkor feltoltjuk a talalati listat id-kkel 
                    }
                }
            }
            
            if(count($results) == 0) {
                echo "A kereses nem hozott talalatot! <br />";
            }
            
            //beolvassuk a pagerankokat tartalmazo fajlt, es soronkent tombbe rakjuk
            //ebben a fajlban lementesre kerult az url is, ezert azt mar nem kell a 3. fajlbol beolvasni
            $filename = "ranks.txt";
            $handle = fopen($filename, "r");
            $contents = fread($handle, filesize($filename));
            fclose($handle);
            
            $rows = explode("\n", $contents);
            $ranks = array(); //pagerankok tombje, [id]->pagerank
            $urls = array();//url-ek tombje, [id]->pagerank

            foreach ($rows as $row) {
                $data = explode(" ", $row);
                if(in_array($data[0], $results)) { //ha megtalaljuk egy sorban az
                                        // id-t, ami a talalati listaban is szerepel, berakjuk az urljet es rankjat
                    $urls[$data[0]] = $data[1];
                    $ranks[$data[0]] = $data[2];
                }
            }
            
            arsort($ranks); //rendezzuk csokkenoen a pagerank listat, a megfelelo id-ket megtartva
            
            foreach($ranks as $id => $rank){ 
                //minden egyes elemhez beolvassuk a lementett html oldalt
                
                $filename = "wiki_depth_500/".$id.".html";
                $handle = fopen($filename, "r");
                $contents = fread($handle, filesize($filename));
                fclose($handle);
                //preg_match("/[^\.]{0,100}?\b(".$query.")\b.{0,200}\./i", $contents, $matches);
                
                $position = stripos($contents, $query); //a heading a talat szo koruli resz
                                                        //ebben az esetben 200 karakteres kornyezete
                $heading = substr($contents, $position - 100, (strlen($query) + 200));
               
                
                $headingarr = explode(" ", $heading); //itt eltuntetjuk az "felbevagott" szavakat
                array_shift($headingarr);
                array_pop($headingarr);
                $heading = implode(" ", $headingarr);
                
                
                preg_match("/<title>(.+)<\/title>/i", $contents, $titletag); //kikeressuk az oldal title-jét
                $title = $urls[$id];
                if(count($titletag) > 0) {
                    if($titletag[0] != "") {
                        $title = $titletag[0];
                    }
                }
                echo "<a href='".$urls[$id]."' style='font-size: 18px; font-weight: bold'>".  strip_tags($title)."</a><br />";
                echo preg_replace('/('.$query.')/i', '<b>\1</b>',htmlentities($heading));
                echo "<br /><br />";
                
            }
        }
        ?>

    </body>
</html>