<?php
header('Content-Type: application/json; charset=utf-8');


$API_KEY = "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";

$input = json_decode(file_get_contents('php://input'), true);
$question = trim($input['question'] ?? '');
$historique = $input['historique'] ?? [];

if (!$question) {
    echo json_encode(['reponse' => "Pose-moi une question."]);
    exit;
}
if (empty($API_KEY) || $API_KEY === "MET_TA_NOUVELLE_CLE_ICI") {
    echo json_encode(['reponse' => "ERREUR_CLE"]);
    exit;
}

$restaurants_texte = "
- Le Grill - Bamako a Hippodrome, Bamako : poulet, poisson, riz, grill
- Sandwich+ a Banankabougou, Bamako : sandwich, tacos, burger, shawarma, frites
- Le Relax a ACI 2000, Bamako : pizza, pate, salade, jus, glace
- Maquis Le Savana a Centre-ville, Bamako : poulet, poisson, riz, soupe
- Chez Fatoumata a Centre-ville, Segou : riz, poulet, soupe
- Le Faso a Centre-ville, Kayes : riz, poulet, poisson
- Maquis Kati a Centre-ville, Kati : poulet, riz, poisson
";

$systeme = "Tu es Muna, l'assistante de l'application Restaurant By Mali au Mali.
Tu reponds UNIQUEMENT sur les restaurants presents sur l'application, liste ci-dessous. Tu ne dois JAMAIS inventer un restaurant qui n'est pas dans la liste.
Si l'utilisateur demande autre chose, tu reponds EXACTEMENT : 'Je reponds uniquement sur les restaurants presents sur l\'application Restaurant By Mali.'
Si l'utilisateur demande 'quel restaurant est le plus proche ?', demande-lui : 'Tu es ou actuellement ?'
Tu parles en francais simple, comme au Mali. Phrases courtes. Tu es gentille.

LISTE DES RESTAURANTS :
$restaurants_texte
";

$messages = [];
$messages[] = ['role' => 'system', 'content' => $systeme];
foreach ($historique as $h) {
    $role = ($h['role'] === 'assistant') ? 'assistant' : 'user';
    $messages[] = ['role' => $role, 'content' => $h['text']];
}
$messages[] = ['role' => 'user', 'content' => $question];

$url = "https://api.groq.com/openai/v1/chat/completions";
$data = [
    "model" => "openai/gpt-oss-120b",
    "messages" => $messages,
    "temperature" => 0.7,
    "max_tokens" => 300
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $API_KEY
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
$result = curl_exec($ch);
$curlErreur = curl_error($ch);
curl_close($ch);

if ($curlErreur) {
    echo json_encode(['reponse' => "Erreur connexion : " . $curlErreur]);
    exit;
}

$json = json_decode($result, true);
$reponse = $json['choices'][0]['message']['content'] ?? "";

if (!$reponse) {
    // Ici on affiche la VRAIE erreur de Groq pour comprendre
    $msgErreur = $json['error']['message'] ?? $result;
    echo json_encode(['reponse' => "Erreur Groq : " . $msgErreur]);
    exit;
}
echo json_encode(['reponse' => trim($reponse)]);