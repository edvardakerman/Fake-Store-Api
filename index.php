<?php

// Headers
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Referrer-Policy: no-referrer");

// Get-request
$show = $_GET['show'] ?? 20;
$category = $_GET['category'] ?? "all";

$errors = array();
$err_msg = file_get_contents("error-msg.json");
$err_msg = json_decode($err_msg, true); 

// Validering
htmlspecialchars($show);
htmlspecialchars($category);

if (!is_numeric($show) || $show > 20 || $show < 1) {
    array_push($errors, $err_msg[0]);
}

if ($category !== "jewelery" && $category !== "electronics" && $category !== "women clothing" && $category !== "men clothing" && $category !== "all" ) {
    array_push($errors, $err_msg[1]);
}
// Felmeddelande Exit vid en ogiltig get-request
if (count($errors) > 0){
    $json = json_encode($errors, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    echo $json;
    exit();
}

// Hämta data från store-data
$data = file_get_contents("store-dataAPI.json"); 
$data = json_decode($data, true); 
$products = [];
$products_final = [];

// Filtrerar genom kategorier
foreach($data as $product) {
    if ($product["category"] == $category) {
        array_push($products, $product);
    } else if ($category == "all"){
        array_push($products, $product);
    }
}; 

// sätter up en array med number, för att slumpa fram rätt typ utav produkt
if ($category == "all"){
    $numbers = range(0, 19);
} else {
    $numbers = range(0, count($products) - 1);
}

// gör om ordningen på numbers
shuffle($numbers);

if ($show >= count($products) + 1){
    $show = count($products);
}

// Går igenom show, pushar in ett item från $products på random index($numbers) in i $products_final  
for ($x = 0; $x < $show; $x++) {
    array_push($products_final, $products[$numbers[$x]]);
};

// Konverterar till json och skickar ut
$json = json_encode($products_final, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
echo $json;