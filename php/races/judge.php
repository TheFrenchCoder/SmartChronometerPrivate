<?php 

//* On démarre la session si besoin dans le futur
session_start();
//* Connnection à la base de donnée
include_once $_SERVER["DOCUMENT_ROOT"]."/include/bdd/bddConnectByRoot.php";
//* TRAITEMENT DES DONNES
include_once $_SERVER["DOCUMENT_ROOT"]."/include/judging.php";
//* NAV BAR
include_once $_SERVER["DOCUMENT_ROOT"]."/include/part/navbar.php";

include_once $_SERVER["DOCUMENT_ROOT"]."/include/json.php";

//Check autorisation a être sur cette page:
if (!in_array($_SESSION['user_role'], $Json_roleAllowToJudge)) {
    echo "Vous n'avez pas accès à cette partie de l'application Web, veuillez retournez a l'acceuil";
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Juges</title>
    <meta charset="utf-8">
    <!-- TODO: Replace "start.css" to "judge.css" -->
    <link rel="stylesheet" type="text/css" href="/css/start.css" />
</head>

<body>

<div class="Title">
    <?php
        $message_count_gates = "(portes: ";
            $first_gate = true;
            $user_gates = $json['judge_gates'][$_SESSION["user_id"]];
            foreach ($user_gates as $gate) {
                        if ($first_gate == false) {
                            $message_count_gates .= ", ";
                        }
                        $message_count_gates .= $gate;
                        $first_gate = false;
                    }
            $message_count_gates .= ")";

        echo "<h1>Juge $message_count_gates</h1>";
    ?>
</div>

<div class="table_ToJudge">
    <?php
        $message_count_gates = "(portes: ";
        $first_gate = true;
        $user_gates = $json['judge_gates'][$_SESSION["user_id"]];
        foreach ($user_gates as $gate) {
                    if ($first_gate == false) {
                        $message_count_gates .= ", ";
                    }
                    $message_count_gates .= $gate;
                    $first_gate = false;
                }
        $message_count_gates .= ")";

        $qToJudge["All"] = $bdd->query('SELECT * FROM competitors WHERE NOT EXISTS (SELECT * FROM penalty WHERE competitors.number = penalty.competitor_number AND competitors.IsOnRun = 1 AND competitors.IsHere = 1)');
        $countToJudge["All"] = $qToJudge["All"] ->rowCount();

        if ($countToJudge["All"] == 0){

            echo "<h2>Vous n’avez plus de compétiteurs</h2>";
        }else {

            echo "<h2>A juger:</h2>";
        ?>            
            <table>
                <div class="TR_ToJudge">
                    </TR>
                        <TH>Dossard</TH>
                        <TH>Nom Prénom</TH>
                        <TH>Club</TH>
                        <TH colspan="4" >Actions</TH>
                    </TR>
                </div>            
    <?php
        }
    
        foreach ($user_gates as $gate) {
            echo "
            </TR>
                <TH colspan='7' > Portes: $gate</TH>
            </TR>";

            $qToJudge[$gate] = $bdd->prepare('SELECT * FROM competitors WHERE NOT EXISTS (SELECT * FROM penalty WHERE competitors.number = penalty.competitor_number AND penalty.gate_number = :gate)');   
            $qToJudge[$gate]->execute(array(':gate' => $gate));

            foreach ($qToJudge[$gate] as $dataToJudge) {
    ?>

    <div class="InitData_ToJudge">
        <?php
        //Extraction et affectation des variables
        // Stocke $data[firstname] dans une variable temporaire
        $TfirstnameToJudge = $dataToJudge['firstname'];
        // Stocke toutes la lettres sauf la 1ere dans une variable temporaire
        $TrestToJudge = substr($TfirstnameToJudge, 1);
        //LowerCase de $Trest en $rest
        $restToJudge = strtolower($TrestToJudge);
        //UpCase de $Tfirstname en $firstname
        $firstnameToJudge = strtoupper($TfirstnameToJudge);
        //UpCase de $data[name] en $name
        $nameToJudge = strtoupper($dataToJudge['name']);
        // Stocke $data[club_abrev] dans une variable temporaire
        $Tclub_abrevToJudge = $dataToJudge['club_abrev'];
        //UpCase de $Tclub_abrev en $club_abrev
        $club_abrevToJudge = strtoupper($Tclub_abrevToJudge);
        ?>
    </div>

    <div class="Dossard_ToJudge">
        <?php
        //Dossard = $data[number]
        echo "</TR> <form method=\"GET\" action=\"\">";
        echo "
            <TD>
            <input type=\"checkbox\" name=\"number\" class=\"dossard-button\" id=\"checkbox\" value=\"$dataToJudge[number]\" checked>
            <label for=\"checkbox\">$dataToJudge[number]</label>

            <input type=\"checkbox\" name=\"gate\" class=\"dossard-button\" id=\"checkbox\" value=\"$gate\" checked>
            </TD>  
            ";
        ?>
    </div>        
    
    <div class="Nom_Prenom_ToJudge">
        <?php
        //"Nom Prénom"=  $name $firstname[0]$rest
        echo "<TD> $nameToJudge $firstnameToJudge[0]$restToJudge </TD>";
        ?>
    </div>

    <div class="Club_ToJudge">
        <?php
        //Club = $club_abrev
        echo "<TD> $club_abrevToJudge </TD>";
        ?>
    </div>
        
    <div class="Submit_ToJudge">
        <?php
        //Start = <input type="Submit">
        if ($Json_JudgeDisplayPenaltyChoice == "text") {
            echo "
            <TD>

            <input id=\"perfect_$dataToJudge[number]\" type=\"submit\" name=\"penalty_amount\" value=\"0\">
            <label for=\"perfect_$dataToJudge[number]\">Aucune</label>

            </TD>

            <TD>

            <input id=\"touched_$dataToJudge[number]\" type=\"submit\" name=\"penalty_amount\" value=\"2\">
            <label for=\"touched_$dataToJudge[number]\">Touchée</label>

            </TD>

            <TD>

            <input id=\"missed_$dataToJudge[number]\" type=\"submit\" name=\"penalty_amount\" value=\"50\">
            <label for=\"missed_$dataToJudge[number]\">Ratée</label>

            </TD>

            </form>
            ";
        } elseif ($Json_JudgeDisplayPenaltyChoice == "number"){
            echo "
            <TD>

            <input id=\"perfect_$dataToJudge[number]\" type=\"submit\" name=\"penalty_amount\" value=\"0\">
            <label for=\"perfect_$dataToJudge[number]\">0</label>

            </TD>

            <TD>

            <input id=\"touched_$dataToJudge[number]\" type=\"submit\" name=\"penalty_amount\" value=\"2\">
            <label for=\"touched_$dataToJudge[number]\">2</label>

            </TD>

            <TD>

            <input id=\"missed_$dataToJudge[number]\" type=\"submit\" name=\"penalty_amount\" value=\"50\">
            <label for=\"missed_$dataToJudge[number]\">50</label>

            </TD>

            ";
        } else {
            $error["JudgeDisplayPenaltyChoice"] = "Erreur dans le fichier de config [judge][displayPenaltyChoice]";
        }

            echo "
            <TD>

            <input id=\"surrend_$dataToJudge[number]\" type=\"submit\" name=\"surrend\" value=\"1\">
            <label for=\"surrend_$dataToJudge[number]\">Abandon</label>

            </TD>
            ";
        ?>
    </div>
        
    <?php
    echo "</TR></form>";  
    }
    }
    ?>
    </form>
    </table>
</div>

<div class="table_HasBeenJudge">
    <?php

        $qHasBeenJudge["All"] = $bdd->query('SELECT * FROM competitors, penalty WHERE competitors.number = penalty.competitor_number AND EXISTS (SELECT * FROM competitors WHERE competitors.number = penalty.competitor_number AND competitors.IsOnRun = 1 AND competitors.IsHere = 1) ORDER BY penalty.gate_number, penalty.id');
        $countHasBeenJudge["All"] = $qHasBeenJudge["All"] ->rowCount();

        if ($countHasBeenJudge["All"] == 0){
            echo "<h2>Ont été jugé: 0</h2>";
        }else {
            echo "<h2>Ont été jugé:".$countHasBeenJudge['All']."</h2>";
        ?>            
            <table>
                <div class="TR_HasBeenJudge">
                    </TR>
                        <TH>Dossard</TH>
                        <TH>Nom Prénom</TH>
                        <TH>Club</TH>
                        <TH colspan="2" >Pénalitées</TH>
                    </TR>
                </div>            

    <?php
        }

        foreach ($user_gates as $gate) {
            echo "
            </TR>
                <TH colspan=\"7\" > Portes: $gate</TH>
            </TR>";

            $qHasBeenJudge[$gate] = $bdd->prepare('SELECT * FROM competitors, penalty WHERE competitors.number = penalty.competitor_number AND EXISTS (SELECT * FROM competitors WHERE competitors.number = penalty.competitor_number AND penalty.gate_number = :gate AND competitors.IsOnRun = 1 AND competitors.IsHere = 1) ORDER BY penalty.gate_number, penalty.id');
            $parameters_HasBeenJudge[$gate] = array(':gate' => $gate);
            $qHasBeenJudge[$gate]->execute($parameters_HasBeenJudge[$gate]);

        foreach ($qHasBeenJudge[$gate] as $dataHasBeenJudge) {
    ?>

    <div class="InitData_HasBeenJudge">
        <?php
        //Extraction et affectation des variables
        // Stocke $data[firstname] dans une variable temporaire
        $TfirstnameHasBeenJudge = $dataHasBeenJudge['firstname'];
        // Stocke toutes la lettres sauf la 1ere dans une variable temporaire
        $TrestHasBeenJudge = substr($TfirstnameHasBeenJudge, 1);
        //LowerCase de $Trest en $rest
        $restHasBeenJudge = strtolower($TrestHasBeenJudge);
        //UpCase de $Tfirstname en $firstname
        $firstnameHasBeenJudge = strtoupper($TfirstnameHasBeenJudge);
        //UpCase de $data[name] en $name
        $nameHasBeenJudge = strtoupper($dataHasBeenJudge['name']);
        // Stocke $data[club_abrev] dans une variable temporaire
        $Tclub_abrevHasBeenJudge = $dataHasBeenJudge['club_abrev'];
        //UpCase de $Tclub_abrev en $club_abrev
        $club_abrevHasBeenJudge = strtoupper($Tclub_abrevHasBeenJudge);
        $penaltyidHasBeenJudge = $dataHasBeenJudge['id'];
        ?>
    </div>

    <div class="Dossard_HasBeenJudge">
        <?php
        //Dossard = $data[number]
        echo "</TR> <form method=\"GET\" action=\"\">";
        echo "
            <TD>
            <input type=\"checkbox\" name=\"number\" class=\"dossard-button\" id=\"checkbox\" value=\"$dataHasBeenJudge[number]\" checked>
            <label for=\"checkbox\">$dataHasBeenJudge[number]</label>
            </TD>  
            ";
        ?>
    </div>        
    
    <div class="Nom_Prenom_HasBeenJudge">
        <?php
        //"Nom Prénom"=  $name $firstname[0]$rest
        echo "<TD> $nameHasBeenJudge $firstnameHasBeenJudge[0]$restHasBeenJudge </TD>";
        ?>
    </div>

    <div class="Club_HasBeenJudge">
        <?php
        //Club = $club_abrev
        echo "<TD> $club_abrevHasBeenJudge </TD>";
        ?>
    </div>
        
    <div class="Submit_HasBeenJudge">
        <?php

        //Start = <input type="Submit">
            echo "
            <TD>
            
            <p> $dataHasBeenJudge[penalty_amount] <p/>

            <!--//*! Fail de sécurité si tu modifie l'input 'penalty_id' tu remove n'imp quel pénalitées--!>
            <input type=\"text\" readonly=\"readonly\" name=\"penalty_id\" value=\"$dataHasBeenJudge[id]\" style=\"display: none;\" />
            </TD>

            <TD>

            <input id=\"reverse$penaltyidHasBeenJudge\".\"_\".\"$dataHasBeenJudge[number]\" type=\"submit\" name=\"reverse\" value=\"1\">
            <label for=\"reverse$penaltyidHasBeenJudge\".\"_\".\"$dataHasBeenJudge[number]\">Annuler</label>

            </TD>

            </form>
            ";

        ?>
    </div>
        
    <?php
    echo "</TR>";  
    }
    }
  ?>
    <table/>
</div>

</body>
</html>

<?php
//DEBUG
include_once $_SERVER['DOCUMENT_ROOT']."/include/debug.php";

//FOOTER
include_once $_SERVER['DOCUMENT_ROOT']."/include/part/footer.php";

?>