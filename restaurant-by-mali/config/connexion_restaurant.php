<?php
// config/connexion_restaurant.php
// Pour Restaurant/login.html

require_once "db.php";
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$code = trim($data['code_restaurant'] ?? '');
$mot_de_passe = trim($data['mot_de_passe'] ?? '');

if($code === '' || $mot_de_passe === ''){
  echo json_encode(["ok" => false, "message" => "Remplis ton code et ton mot de passe"]);
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM restaurants WHERE code_restaurant = ? AND mot_de_passe = ?");
$stmt->execute([$code, $mot_de_passe]);
$resto = $stmt->fetch(PDO::FETCH_ASSOC);

if($resto){
  // Si la page n'est pas encore activée par l'Admin, on bloque
  if($resto['statut_page'] !== 'active'){
    echo json_encode(["ok" => false, "message" => "Ta page n'est pas encore activée par l'Admin. Attends la validation."]);
    exit;
  }

  $log = $pdo->prepare("INSERT INTO connexions_log (type_user, user_id, action) VALUES ('restaurant', ?, 'connexion')");
  $log->execute([$resto['id']]);

  echo json_encode([
    "ok" => true,
    "message" => "Connexion réussie",
    "restaurant" => [
      "id" => $resto['id'],
      "code_restaurant" => $resto['code_restaurant'],
      "nom" => $resto['nom'],
      "telephone" => $resto['telephone'],
      "categorie" => $resto['categorie']
    ]
  ]);
} else {
  echo json_encode(["ok" => false, "message" => "Code ou mot de passe incorrect"]);
}
?>