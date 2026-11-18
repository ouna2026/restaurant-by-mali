<?php
// config/connexion_admin.php
// Pour Admin/login.html

require_once "db.php";
header('Content-Type: application/json');

// On récupère ce que le login.html envoie
$data = json_decode(file_get_contents("php://input"), true);
$telephone = trim($data['telephone'] ?? '');
$mot_de_passe = trim($data['mot_de_passe'] ?? '');

if($telephone === '' || $mot_de_passe === ''){
  echo json_encode(["ok" => false, "message" => "Remplis téléphone et mot de passe"]);
  exit;
}

// On cherche l'admin dans la table admins
$stmt = $pdo->prepare("SELECT * FROM admins WHERE telephone = ? AND mot_de_passe = ?");
$stmt->execute([$telephone, $mot_de_passe]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if($admin){
  // On note la connexion dans connexions_log
  $log = $pdo->prepare("INSERT INTO connexions_log (type_user, user_id, action) VALUES ('admin', ?, 'connexion')");
  $log->execute([$admin['id']]);

  echo json_encode([
    "ok" => true,
    "message" => "Connexion réussie",
    "admin" => [
      "id" => $admin['id'],
      "nom" => $admin['nom'],
      "prenom" => $admin['prenom'],
      "telephone" => $admin['telephone']
    ]
  ]);
} else {
  echo json_encode(["ok" => false, "message" => "Téléphone ou mot de passe incorrect"]);
}
?>