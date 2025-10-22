<?php
use App\View\View;
use App\Support\Version;

require "./inc/config.inc.php";

$tplAbout = new View("templates/about");

// $text="Doriedson Alves Galdino de Olivei, 15689 abcedfghijklhagsfdteudikfjdyh casa 2";

// $columns = 40;

// $textlen = mb_strlen(($text));

// $new_text = "";

// $arr = explode(" ", $text);

// $text_tmp = "";

// for ($index = 0; $index < count($arr); $index++) {

//   if (mb_strlen($text_tmp . " " . $arr[$index]) > $columns) {

//     if ($text_tmp == "") {

//       $text_tmp2 = $arr[$index];

//       while (mb_strlen($text_tmp2) > 0) {

//         if (mb_strlen($text_tmp2) > $columns) {

//           $new_text .= mb_substr($text_tmp2, 0, $columns) . "<br>";

//           $text_tmp2 = mb_substr($text_tmp2, $columns, mb_strlen($text_tmp2) - $columns);

//         } else {

//           $text_tmp = $text_tmp2;

//           $text_tmp2 = "";
//         }
//       }

//     } else {

//       $new_text .= $text_tmp . "<br>";

//       $text_tmp = "";

//       $index--;
//     }

//   } else {

//     $text_tmp .= " " . $arr[$index];
//   }
// }

// $new_text .= $text_tmp . "<br>";
// $connector = new NetworkPrintConnector("192.168.1.3");
// $connector->finalize();
// $connector1 = new NetworkPrintConnector("192.168.1.3");
// $connector->finalize();

$data = [
  "version" => Version::get()
];

Send($tplAbout->getContent($data, "BLOCK_PAGE"));