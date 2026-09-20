<?php

// error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

$teams = [
   'Flamengo',
   'Corinthians',
   'Palmeiras',
   'São Paulo',
   'Fluminense',
   'Botafogo',
   'Santos',
   'Bahia',
   'Ceará',
   'Fortaleza'
];

function generateAllBattlesResults(array $aryTeams) {
   
   $allBattlesResults = [];
   
   foreach ($aryTeams as $kTeam => $vTeam) {

      $getAllTeamsWithoutCurrent = $aryTeams;
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

function calcChampionshipResults(array $aryAllMatchs, array $teams) {

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

      // var_dump($aryAllMatchs);

      $gamesTeam = array_filter(
         $aryAllMatchs,
         function($match) use ($vTeam) {
            if (in_array($vTeam, array_values(array_keys($match)))) {
               return $match;
            } 
         }
      );

      // var_dump($gamesTeam);

      foreach ($gamesTeam as $k => $match) {
         // var_dump($match);

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

            $balanceGoals = $pointsGoals - $match[$vTeam];

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

$allGamesResults = generateAllBattlesResults($teams);

// var_dump($allGamesResults);

$allTResults = calcChampionshipResults($allGamesResults, $teams);

var_dump($allTResults);

?>