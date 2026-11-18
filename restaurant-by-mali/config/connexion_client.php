<?php
// config/connexion_client.php
// Pour Client/login.html

require_once "db.php";
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$telephone = trim($data['telephone'] ?? '');
$mot_de_passe = trim($data['mot_de_passe'] ?? '');

if($telephone === '' || $mot_de_passe === ''){
  echo json_encode(["ok" => false, "message" => "Remplis téléphone et mot de passe"]);
  exit;
}

$stmt = $pdo->prepare("SELECT * FROM clients WHERE telephone = ? AND mot_de_passe = ?");
$stmt->execute([$telephone, $mot_de_passe]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

if($client){
  // On met à jour la dernière connexion
  $upd = $pdo->prepare("UPDATE clients SET derniere_connexion = NOW() WHERE id = ?");
  $upd->execute([$client['id']]);

  // On note dans le journal
  $log = $pdo->prepare("INSERT INTO connexions_log (type_user, user_id, action) VALUES ('client', ?, 'connexion')");
  $log->execute([$client['id']]);

  echo json_encode([
    "ok" => true,
    "message" => "Connexion réussie",
    "client" => [
      "id" => $client['id'],
      "nom" => $client['nom'],
      "telephone" => $client['telephone'],
      "points" => $client['points'],
      "niveau" => $client['niveau']
    ]
  ]);
} else {
  echo json_encode(["ok" => false, "message" => "Téléphone ou mot de passe incorrect"]);
}
?>