<?php
session_start();
$winMessage = "Vous avez trouvé le trésor !!!";

function generateMaze()
{
    $maze1 = [
        [1, 0, 0, 2, 0],
        [2, 0, 0, 0, 2],
        [0, 0, 2, 0, 2],
        [0, 0, 2, 0, 0],
        [2, 0, 0, 0, 2],
        [0, 0, 2, 0, 3],
    ];

    $maze2 = [
        [1, 0, 2, 0, 0, 0, 0, 2, 0, 0],
        [2, 0, 0, 0, 2, 0, 0, 0, 0, 3],
        [0, 0, 0, 0, 2, 0, 0, 2, 0, 0],
        [2, 0, 2, 0, 0, 0, 0, 0, 0, 2],
    ];

    $randomMaze = (rand(0, 1) == 0) ? $maze1 : $maze2;
    return $randomMaze;
}

function displayMaze($maze, $currentPosition, $numCols)
{
    echo "<div style='display: grid; grid-template-columns: repeat(" . $numCols . ", 50px); margin: auto; width: fit-content;'>";

    for ($i = 0; $i < count($maze); $i++) {
        for ($j = 0; $j < $numCols; $j++) {
            echo "<div style='width: 50px; height: 50px; border: 0px solid #000; text-align: center; vertical-align: middle; margin: auto;";

            $distance = abs($i - $currentPosition[0]) + abs($j - $currentPosition[1]);

            if ($distance <= 1 || ($i == $currentPosition[0] && $j == $currentPosition[1])) {
                // Révèle les cases autour du pirate
                if ($i == $currentPosition[0] && $j == $currentPosition[1]) {
                    echo " background-color: white;"; // Met en évidence la position actuelle du pirate
                }

                echo "'>";

                $cellValue = isset($maze[$i][$j]) ? $maze[$i][$j] : null;

                switch ($cellValue) {
                    case 0:
                        break;
                    case 1:
                        echo "<img src='./assets/images/22656111_pirates_06.jpg' alt='Pirate' width='50' height='50'>";
                        break;
                    case 2:
                        echo "<img src='./assets/images/ile.jpg' alt='Île' width='50' height='50'>";
                        break;
                    case 3:
                        echo "<img src='./assets/images/tresor.jpg' alt='Trésor' width='50' height='50'>";
                        break;
                    default:
                        break;
                }
            } else {
                // Affiche des vagues pour le brouillard de guerre
                echo "'>";
                echo "<img src='./assets/images/vague-removebg-preview.png' alt='Vague' width='50' height='50'>";
            }

            echo "</div>";
        }
    }

    echo "</div>";
}


if ($_SESSION['maze']===null || isset($_POST['reset'])) {
    $_SESSION['maze']=null;
    $_SESSION['maze'] = generateMaze();
    $_SESSION['position'] = [0, 0];
    $_SESSION['win'] = null;
    $_SESSION['error'] = null;
}


if (isset($_POST['move'])) {
    $moveDirection = $_POST['move'];

    $row = $_SESSION['position'][0];
    $col = $_SESSION['position'][1];

    $newRow = $row;
    $newCol = $col;

    

    switch ($moveDirection) {
        case 'up':
            $newRow = max(0, $row - 1);
            break;
        case 'down':
            $newRow = min(count($_SESSION['maze']) - 1, $row + 1);
            break;
        case 'left':
            $newCol = max(0, $col - 1);
            break;
        case 'right':
            $newCol = min(count($_SESSION['maze'][0]) - 1, $col + 1);
            break;
    }

    // Vérifie si la nouvelle position est valide (pas une ile)
    if ($_SESSION['maze'][$newRow][$newCol] != 2) {
        $_SESSION['error'] = '';
        if ($_SESSION['maze'][$newRow][$newCol] == 3) {
            $_SESSION['win'] = $winMessage;
        }
        else{
            // Déplacez le pirate
            $_SESSION['maze'][$row][$col] = 0; // La case actuelle du pirate devient vide
            $_SESSION['maze'][$newRow][$newCol] = 1; // La nouvelle case devient la position du pirate
            $_SESSION['position'] = [$newRow, $newCol]; // Met à jour la position du pirate
        }
        
    }
    else{
        $_SESSION['error'] = 'Vous ne pouvez pas accoster !';
    }
    
}



?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/style.css">
    <title>LABYRINTHE</title>
</head>

<body>

    <h1 style='width: fit-content; margin: auto; margin-bottom: 100px;'>Trouve le trésor !</h1>
    
    <p id="winMessage" ><?= $_SESSION['win'] !== null ? $_SESSION['win'] :''?></p>
    <p id='errorMessage' ><?= $_SESSION['error'] !== null ? $_SESSION['error'] : '' ?></p>
    <form method="POST">
        <?php
        displayMaze($_SESSION['maze'], $_SESSION['position'], count($_SESSION['maze'][0]));
        ?>
        <br>
        <div id="buttonContainer">
            <button id="up" class="button" type="submit" name="move" value="up"><img style="width:50px; height:50px;"
                    src="./assets/images/up-arrow.png"></button>
            <button id="down" class="button" type="submit" name="move" value="down"><img
                    style="width:50px; height:50px;" src="./assets/images/down-arrow.png"></button>
            <button id="left" class="button" type="submit" name="move" value="left"><img
                    style="width:50px; height:50px;" src="./assets/images/left-arrow.png"></button>
            <button id="right" class="button" type="submit" name="move" value="right"><img
                    style="width:50px; height:50px;" src="./assets/images/right-arrow.png"></button>
            <button id="reset" type="submit" name="reset"><img style="width:50px; height:50px;"
                    src="./assets/images/restart-button.png"></button>
        </div>
    </form>
</body>

</html>