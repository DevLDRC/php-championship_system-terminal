<?php

$teams = [
   'Flamengo',
   'Corinthians',
   'Palmeiras',
   'Grêmio',
   'Fluminense',
   'Botafogo',
   'Santos',
   'Bahia',
   'Ceará',
   'Fortaleza'
];

function findTeamFromInput(string $teamName, array $teams): Bool {

   $teamsList = array_map(
      function($team) {
         return mb_strtoupper($team, 'UTF-8');
      },
      $teams
   );

   return in_array(strtoupper($teamName), $teamsList);

}

function showChampionshipTeams(array $teams) {

   foreach ($teams as $k => $v) {
      
      echo "- $v\n";

   }

}

function generateAllBattlesResults(array $teams) {
   
   $allBattlesResults = [];
   
   foreach ($teams as $kTeam => $vTeam) {

      $getAllTeamsWithoutCurrent = $teams;
      array_splice($getAllTeamsWithoutCurrent, $kTeam, $kTeam);
      
      foreach ($getAllTeamsWithoutCurrent as $k => $v) {

         $resultGoalsCurrentTeam = rand(0,7);
         $resultGoalsOponentTeam = rand(0,7);

         $matchResult = array(
            $vTeam => $resultGoalsCurrentTeam,
            $v => $resultGoalsOponentTeam
         );
         
         $allBattlesResults[] = $matchResult;
      };

   };

   array_shift($allBattlesResults);

   return $allBattlesResults;

}

function calcChampionshipResults(array $allBattlesResults, array $teams) {

   $allTeamsResults = [];

   foreach ($teams as $kTeam => $vTeam) {

      $games = 0;
      $wins = 0;
      $tie = 0;
      $losses = 0;
      $pointsGoals = 0;
      $hitsGoals = 0;
      $balanceGoals = 0;
      $pointsChampionship = 0;

      $gamesTeam = array_filter(
         $allBattlesResults,
         function($match) use ($vTeam) {
            if (in_array($vTeam, array_values(array_keys($match)))) {
               return $match;
            } 
         }
      );

      foreach ($gamesTeam as $k => $match) {

         $currentOponentTeam = "";

         if (in_array($vTeam, array_values(array_keys($match)))) {

            if(array_keys($match)[0] === $vTeam) {
               $currentOponentTeam = array_keys($match)[1];
            } else {
               $currentOponentTeam = array_keys($match)[0];
            }

            $games += 1;

            if ($match[$vTeam] > $match[$currentOponentTeam]) {
               $wins += 1;
               $pointsChampionship += 3;
            } elseif ($match[$vTeam] === $match[$currentOponentTeam]) {
               $tie += 1;
               $pointsChampionship += 1;
            } elseif ($match[$vTeam] < $match[$currentOponentTeam]) {
               $losses += 1;
            }

            $pointsGoals += $match[$vTeam];

            $hitsGoals += $match[$currentOponentTeam];

            $balanceGoals += $pointsGoals - $match[$vTeam];

         }

      }
      
      $allTeamsResults['Teams'][] = array(
         "$vTeam" => array(
            "Games" => $gamesTeam,
            "Stats" => [
               'games' => $games,
               'wins' => $wins,
               'tie' => $tie,
               'losses' => $losses,
               'pointsGoals' => $pointsGoals,
               'hitsGoals' => $hitsGoals,
               'balanceGoals' => $balanceGoals,
               'pointsChampionship' => $pointsChampionship,
            ]
         )
      );

      // var_dump($allTeamsResults);

   };

   return $allTeamsResults;
   
};

function sortChampionshipRank(array $championshipResults) {

   $championshipListSorted = $championshipResults['Teams'];

   // Persistindo o empate, ordem alfabética do time
   usort($championshipListSorted, function ($tA, $tB) {
      $teamA = array_key_first($tA);
      $teamB = array_key_first($tB);

      return strcoll($teamA, $teamB);
   });

   // Em caso de empate, maior número de gols marcados
   usort($championshipListSorted, function ($tA, $tB) {

      $statsTeamA = reset($tA)['Stats'];
      $statsTeamB = reset($tB)['Stats'];

      return $statsTeamB['pointsGoals'] <=> $statsTeamA['pointsGoals'];
   });

   // Em caso de empate, maior saldo de gols
   usort($championshipListSorted, function ($tA, $tB) {

      $statsTeamA = reset($tA)['Stats'];
      $statsTeamB = reset($tB)['Stats'];

      return $statsTeamB['balanceGoals'] <=> $statsTeamA['balanceGoals'];
   });

   // Maior número de pontos
   usort($championshipListSorted, function ($tA, $tB) {

      $statsTeamA = reset($tA)['Stats'];
      $statsTeamB = reset($tB)['Stats'];

      return $statsTeamB['pointsChampionship'] <=> $statsTeamA['pointsChampionship'];
   });

   return $championshipListSorted;

};

function showChampionshipRank(array $championshipRank) {

   foreach ($championshipRank as $k => $v) {

      $championshipPosition = $k + 1;
   
      echo "\n\n";

      $currentTeamName = array_key_first($v);

      echo "$championshipPosition. $currentTeamName \n";

      echo "Pontos: ";
      echo $v[$currentTeamName]['Stats']['pointsChampionship']; 
      echo "\n";

      echo "Saldo: ";
      echo $v[$currentTeamName]['Stats']['balanceGoals']; 
      echo "\n";

      echo "Gols marcados: ";
      echo $v[$currentTeamName]['Stats']['pointsGoals'];
      echo "\n";

      echo "Vitórias: ";
      echo $v[$currentTeamName]['Stats']['wins']; 
      echo "\n";

      echo "Derrotas: ";
      echo $v[$currentTeamName]['Stats']['losses']; 
      echo "\n";

      echo "Gols sofridos: ";
      echo $v[$currentTeamName]['Stats']['hitsGoals'];
      echo "\n";

      echo "Jogos: ";
      echo $v[$currentTeamName]['Stats']['games'];
      echo "\n";

   }

}

function showTeamRank(string $teamName, array $championshipRank) {

   foreach ($championshipRank as $k => $v) {
      
      $currentTeamName = array_key_first($v);
      $championshipPosition = $k + 1;

      if (strtoupper($currentTeamName) === strtoupper($teamName)) {
      
         echo "\n";
   
         echo "$championshipPosition. $currentTeamName \n";
   
         echo "Pontos: ";
         echo $v[$currentTeamName]['Stats']['pointsChampionship']; 
         echo "\n";
   
         echo "Saldo: ";
         echo $v[$currentTeamName]['Stats']['balanceGoals']; 
         echo "\n";
   
         echo "Gols marcados: ";
         echo $v[$currentTeamName]['Stats']['pointsGoals'];
         echo "\n";
   
         echo "Vitórias: ";
         echo $v[$currentTeamName]['Stats']['wins']; 
         echo "\n";
   
         echo "Derrotas: ";
         echo $v[$currentTeamName]['Stats']['losses']; 
         echo "\n";
   
         echo "Gols sofridos: ";
         echo $v[$currentTeamName]['Stats']['hitsGoals'];
         echo "\n";
   
         echo "Jogos: ";
         echo $v[$currentTeamName]['Stats']['games'];
         echo "\n";

         break;

      }

   }

}

$allBattlesResults = generateAllBattlesResults($teams);
// var_dump($allGamesResults);

$championshipResults = calcChampionshipResults($allBattlesResults, $teams);
// var_dump($championshipResults);

$championshipRank = sortChampionshipRank($championshipResults);
// var_dump($championshipRank);

// -------------------------------------------------- //

showChampionshipRank($championshipRank);

while (true) {

   echo "\n";

   echo 'Digite "1" para ver o rank do campeonato | Digite "2" para ver os times existentes';

   echo "\n";

   $teamName = readline("Busque por um time: ");

   if ($teamName === '1') {
      echo "\n";
      showChampionshipRank($championshipRank);
      continue;
   }

   if ($teamName === '2') {
      echo "\n";
      showChampionshipTeams($teams);
      continue;
   }
   
   if(!findTeamFromInput($teamName, $teams)) {
      echo "\n";
      echo "Esse time não existe ou não esta no campeonato, digite o nome de um time. \n\n---\n";
      continue;
   };

   showTeamRank($teamName, $championshipRank);

}

?>