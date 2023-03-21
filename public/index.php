<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT,DELETE");


use App\Models\Db;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addRoutingMiddleware();
$app->add(new BasePathMiddleware($app));
$app->addErrorMiddleware(true, true, true);

$app->get('/', function (Request $request, Response $response) {
   $response->getBody()->write('Soyez la bienvenue!');
   return $response;
});

// Afficher tous les roles

$app->get('/roles/all', function (Request $request, Response $response,array $args) {
    $sql = "SELECT * FROM roles";
   
    try {
      $db = new Db();
      $conn = $db->connect();
      $stmt = $conn->query($sql);
      $roles = $stmt->fetchAll(PDO::FETCH_OBJ);
      $db = null;
     
      $response->getBody()->write(json_encode($roles));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
    } catch (PDOException $e) {
      $error = array(
        "message" => $e->getMessage()
      );
   
      $response->getBody()->write(json_encode($error));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }
   });

   //inserer un role à la table role

   $app->post('/roles/add', function (Request $request, Response $response) {
  
    $data = $request->getBody()->getContents();
    
    $roles = json_decode($data, true);
    $role_name = $roles['role_name'];
     
    $sql = "INSERT INTO roles(role_name) VALUES (:role_name)";
   
    try {
      $db = new Db();
      $conn = $db->connect();
      $q = $conn->prepare($sql);
     
      $q->bindParam(':role_name',$role_name);
      $stmt = $q->execute();
      
      $res = [
        'message' => "Enregistres avec succes",
        "status" => 200
      ];
      $db = null;
     
      $response->getBody()->write(json_encode($res));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
  
    } catch (PDOException $e) {
      $error = array(
        "message" => $e->getMessage()
      );
   
      $response->getBody()->write(json_encode($error));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(407);
    }
   });

    // UPDATE roles
 $app->put('/roles/update/{role_id}', function (Request $request, Response $response) {
  
  $role_id = $request->getAttribute('role_id');
  $data = $request->getBody()->getContents();

  $roles = json_decode($data, true);
  $role_id = $roles['role_id'];
  $role_name = $roles['role_name'];
  $sql = "UPDATE roles
  SET 
  role_id = :role_id, 
  role_name = :role_name 
  WHERE role_id = :role_id";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
    $q->bindParam(':role_id',$role_id);
    $q->bindParam(':role_name',$role_name);
    
    
    $q->execute();
    
    $res = [
      'message' => "Update successfully",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });
 // DELETE roles

$app->delete('/roles/delete/{role_id}', function (Request $request, Response $response, array $args) {
  $id = $args['role_id'];
  $request->getAttribute('role_id');

  $sql = "DELETE FROM roles WHERE role_id = ". $id;
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
     $q->execute();
    
    $res = [
      'message' => "Suppression avec succes",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });
  

//START TABLE USER

//AFFICHER toutes les informations users 
$app->get('/utilisateur/all', function (Request $request, Response $response,array $args) {
  $sql = "SELECT * FROM utilisateur";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($customers));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });
 
//insertion des informations utilisateurs

$app->post('/utilisateur/add', function (Request $request, Response $response) {
  
  $data = $request->getBody()->getContents();
  $utilisateur = json_decode($data, true);
  $nom= $utilisateur['nom'];
  $prenom= $utilisateur['prenom'];
  $email= $utilisateur['email'];
  $contact= $utilisateur['contact'];
  $pseudo= $utilisateur['pseudo'];
  $pwd=$utilisateur['pwd'];
  $role_id=2;
  
  // Vérification du nom
  if (empty($nom) ){
    $res = [      'message' => "Le nom de l'administrateur est obligatoire",      "status" => 400    ];
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(400);
  }
   //verification du prenom
  
  if(empty($prenom)){
  $res = [      'message' => "veillez renseignez le prenom svp!!",      "status" => 400    ];
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(400);
  }

  // Vérification de l'adresse e-mail

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $res = [      'message' => "L'adresse e-mail n'est pas valide", "status" => 400    ];
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(400);
  }
    
   //verification du contact
  
  if (empty($contact)){
  $res = [      'message' => "veillez renseignez le contact svp!!", "status" => 400    ];
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(400);
  }

 //verification du nom utilisateur
  
  if (empty($pseudo)){
  $res = [      'message' => "veillez renseignez le nom utilisateur svp !!", "status" => 400    ];
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(400);
  }


  // Vérification de l'existence de l'email dans la base de données

  $sql_email = "SELECT email FROM utilisateur WHERE email = :email";
  try {
    $db = new Db();
    $conn = $db->connect();
   
    $q = $conn->prepare($sql_email);
    $q->bindParam(':email',$email, PDO::PARAM_STR);
    $q->execute();

    $result = $q->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
      $res = [        'message' => "cette adresse e-mail existe déjà dans la base de données",        "status" => 409      ];
      $response->getBody()->write(json_encode($res));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(409);
    }

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );

    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(500);
  }
  
  // Vérification du mot de passe

  if (!preg_match('/[a-zA-Z0-9]/', $pwd)) {
    $res = [      'message' => "Le mot de passe doit contenir au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial et avoir une longueur minimale de 8 caractères",      "status" => 400    ];
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(400);
  }
  
  // Inserer dans la base de données

  $sql = "INSERT INTO utilisateur(nom,prenom,email,contact,pseudo,pwd,role_id) VALUES (:nom, :prenom, :email, :contact, :pseudo, :pwd, :role_id)";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
    $q->bindParam(':nom', $nom, PDO::PARAM_STR);
     $q->bindParam(':prenom', $prenom, PDO::PARAM_STR);
    $q->bindParam(':email', $email, PDO::PARAM_STR);
    $q->bindParam(':contact', $contact, PDO::PARAM_STR);
    $q->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
    $q->bindParam(':pwd', $pwd, PDO::PARAM_STR);
    $q->bindParam(':role_id', $role_id, PDO::PARAM_STR);
    $stmt = $q->execute();
    
    $res = [
      'message' => "Votre compte a été crée avec succes",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });

 //____________LOGIN_UTILISATEUR____________

$app->post('/utilisateur/login', function (Request $request, Response $response) {
  
  $data = $request->getBody()->getContents();
  $utilisateur = json_decode($data, true);
  $pseudo = $utilisateur['pseudo'];
  $pwd = $utilisateur['pwd'];


  // Vérification de l'existence de l'email et du mot de passe dans la base de données

  $sql = "SELECT * FROM utilisateur WHERE pseudo = :pseudo AND pwd = :pwd";
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
    $q->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
    $q->bindParam(':pwd', $pwd, PDO::PARAM_STR);
    $q->execute();

    $result = $q->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) == 0) {
      $res = [
        'message' => " nom utilisateur ou mot de passe incorrect",
        "status" => 401
      ];
      $response->getBody()->write(json_encode($res));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(401);
    } else {
      // Génération de la session et envoi de la réponse
      $token = bin2hex(random_bytes(32));
      $_SESSION['token'] = $token;
      $_SESSION['utilisateur_id'] = $result[0]['utilisateur_id'];
      $res = [
        'message' => "Connexion réussie",
        "status" => 200,
        "token" => $token
      ];
      $response->getBody()->write(json_encode($res));
      return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
    }

  } catch (PDOException $e) {
    $error = [
      "message" => $e->getMessage()
    ];

    $response->getBody()->write(json_encode($error));
    return $response
    ->withHeader('content-type', 'application/json')
    ->withStatus(407);
}
});

 //suppression d'un utilisateur

 $app->delete('/utilisateur/delete/{utilisateur_id}', function (Request $request, Response $response, array $args) {
  $id = $args['utilisateur_id'];
  $request->getAttribute('utilisateur_id');

  $sql = "DELETE FROM utilisateur WHERE utilisateur_id = ". $id;
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
     $q->execute();
    
    $res = [
      'message' => "Supprimé avec succes",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });

 // UPDATE /utilisateur
 $app->put('/utilisateur/update/{utilisateur_id}', function (Request $request, Response $response) {
  
  $utilisateur_id = $request->getAttribute('utilisateur_id');
  $data = $request->getBody()->getContents();

  $utilisateur = json_decode($data, true);
  $utilisateur_id = $utilisateur['utilisateur_id'];
  $nom = $utilisateur['nom'];
  $prenom= $utilisateur['prenom'];
  $email= $utilisateur['email'];
  $contact= $utilisateur['contact'];
  $sql = "UPDATE utilisateur
  SET utilisateur_id = :utilisateur_id, nom = :nom, prenom = :prenom, email = :email, contact = :contact
  WHERE utilisateur_id = :utilisateur_id";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
    $q->bindParam(':utilisateur_id',$utilisateur_id);
    $q->bindParam(':nom',$nom);
    $q->bindParam(':prenom',$prenom);
    $q->bindParam(':email',$email);
    $q->bindParam(':contact',$contact);
    
    $q->execute();
    
    $res = [
      'message' => "Update successfully",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });

 //START COUNTRY
 
//lister des pays  
$app->get('/pays/all', function (Request $request, Response $response,array $args) {
  $sql = "SELECT * FROM pays";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $pays = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($pays));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });

//Inserer un pays 
 $app->post('/pays/add', function (Request $request, Response $response) {
  $data = $request->getBody()->getContents();
  $pays = json_decode($data, true);
  $nom= $pays['nom'];
  $localisation= $pays['localisation'];
  $sql = "INSERT INTO pays(nom,localisation) VALUES (:nom,:localisation)";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
    $q->bindParam(':nom',$nom);
    $q->bindParam(':localisation',$localisation);
     $q->execute();
    $res = [
      'message' => "Enregistrés avec succes",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });
  
// //Inserer un pays 
//  $app->post('/pays/add', function (Request $request, Response $response) {
//   $data = $request->getBody()->getContents();
//   $utilisateur = json_decode($data, true);
//   $nom= $utilisateur['nom'];
//   $localisation= $utilisateur['localisation'];
//   $sql = "INSERT INTO pays(nom,localisation) VALUES (:nom,:localisation)";
 
//   try {
//     $db = new Db();
//     $conn = $db->connect();
//     $q = $conn->prepare($sql);
//     $q->bindParam(':nom',$nom);
//     $q->bindParam(':localisation',$localisation);
//      $q->execute();
//     $res = [
//       'message' => "Enregistrés avec succes",
//       "status" => 200
//     ];
//     $db = null;
   
//     $response->getBody()->write(json_encode($res));
//     return $response
//       ->withHeader('content-type', 'application/json')
//       ->withStatus(200);

//   } catch (PDOException $e) {
//     $error = array(
//       "message" => $e->getMessage()
//     );
 
//     $response->getBody()->write(json_encode($error));
//     return $response
//       ->withHeader('content-type', 'application/json')
//       ->withStatus(407);
//   }
//  });
//Mise à jour d'un pays

 $app->put('/pays/update/{pays_id}', function (Request $request, Response $response) {
  
  $pays_id = $request->getAttribute('pays_id');
  $data = $request->getBody()->getContents();

  $pays = json_decode($data, true);
  $pays_id = $pays['pays_id'];
  $nom = $pays['nom'];
  $localisation= $pays['localisation'];

  $sql = "UPDATE pays
  SET pays_id = :pays_id, nom = :nom, localisation = :localisation
  WHERE pays_id = :pays_id";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
    $q->bindParam(':pays_id',$pays_id);
    $q->bindParam(':nom',$nom);
    $q->bindParam(':localisation',$localisation);
    $q->execute();
    
    $res = [
      'message' => "Update successfully",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });

 //suppression d'un pays
 $app->delete('/pays/delete/{pays_id}', function (Request $request, Response $response, array $args) {
  $id = $args['pays_id'];
  $request->getAttribute('pays_id');

  $sql = "DELETE FROM pays WHERE pays_id = ". $id;
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
     $q->execute();
    
    $res = [
      'message' => "Suppression réussie",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });



 //Rechercher un pays
 $app->get('/pays/item/{pays_id}', function (Request $request, Response $response, array $args) {
  $id = $args['pays_id'];
  $request->getAttribute('pays_id');

  $sql = "SELECT * FROM pays WHERE pays_id = ". $id;
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $pays = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($pays));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });

//START COMPANY
 
//lister des compagnies 
$app->get('/compagnie/all', function (Request $request, Response $response,array $args) {
  $sql = "SELECT * FROM compagnie";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $compagnie = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
   
    $response->getBody()->write(json_encode($compagnie));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });

 //recupérer un enregistrement d'une compagnie

$app->get('/compagnie/one/{compagnie_id}', function (Request $request, Response $response,array $args) {
  $id = $args['compagnie_id'];
  $request->getAttribute('compagnie_id');
  $sql = "SELECT * FROM compagnie WHERE compagnie_id=".$id;
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $compagnie = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    $response->getBody()->write(json_encode($compagnie));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });




//Inserer une compagnie 
 $app->post('/compagnie/add', function (Request $request, Response $response) {
  $data = $request->getBody()->getContents();
  $compagnie = json_decode($data, true);
  $nom= $compagnie['nom'];
  $adresse= $compagnie['adresse'];
  $pays_id= $compagnie['pays_id'];
  $sql = "INSERT INTO compagnie(nom,adresse,pays_id) VALUES (:nom,:adresse, :pays_id)";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
    $q->bindParam(':nom',$nom);
    $q->bindParam(':adresse',$adresse);
    $q->bindParam(':pays_id',$pays_id);
     $q->execute();
    $res = [
      'message' => "Enregistrement réussi",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });
//Mise à jour d'une compagnie

 $app->put('/compagnie/update/{compagnie_id}', function (Request $request, Response $response) {
  
  $compagnie_id = $request->getAttribute('compagnie_id');
  $data = $request->getBody()->getContents();

  $compagnie = json_decode($data, true);
  $compagnie_id = $compagnie['compagnie_id'];
  $nom = $compagnie['nom'];
  $adresse= $compagnie['adresse'];
  $pays_id=$compagnie['pays_id'];
   
  $sql = "UPDATE compagnie 
  SET compagnie_id = :compagnie_id, nom = :nom, adresse = :adresse, pays_id = :pays_id
  WHERE compagnie_id = :compagnie_id";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
    $q->bindParam(':compagnie_id',$compagnie_id);
    $q->bindParam(':nom',$nom);
    $q->bindParam(':adresse',$adresse);
    $q->bindParam(':pays_id',$pays_id);
    $q->execute();
    
    $res = [
      'message' => "Update successfully",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });

 //suppression d'une compagnie

 $app->delete('/compagnie/delete/{compagnie_id}', function (Request $request, Response $response, array $args) {
  $id = $args['compagnie_id'];
  $request->getAttribute('compagnie_id');
  $sql = "DELETE FROM compagnie WHERE compagnie_id = ". $id;
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
     $q->execute();
    
    $res = [
      'message' => "Suppression réussie",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });


//GESTION ARMOIRE

//lister des armoires 
$app->get('/armoire/all', function (Request $request, Response $response,array $args) {
  $sql = "SELECT * FROM armoire";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $compagnie = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    $response->getBody()->write(json_encode($compagnie));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });

 //recuperer un enregistrement d'une armoire
 $app->get('/armoire/one/{armoire_id}', function (Request $request, Response $response,array $args) {
  $id = $args['armoire_id'];
  $request->getAttribute('armoire_id');
  $sql = "SELECT * FROM armoire WHERE armoire_id=".$id;
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $compagnie = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    $response->getBody()->write(json_encode($compagnie));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });


//Inserer une armoire
 $app->post('/armoire/add', function (Request $request, Response $response) {
  $data = $request->getBody()->getContents();
  $armoire = json_decode($data, true);
  // $armoire_id= $armoire['armoire_id'];
  $code_armoire= $armoire['code_armoire'];
  $description= $armoire['description'];
  $compagnie_id= $armoire['compagnie_id'];
  $sql = "INSERT INTO armoire(code_armoire,description,compagnie_id) VALUES (:code_armoire,:descriptions,:compagnie_id)";
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
    // $q->bindParam(':armoire_id',$armoire_id);
    $q->bindParam(':code_armoire',$code_armoire);
    $q->bindParam(':descriptions',$description);
    $q->bindParam(':compagnie_id',$compagnie_id);
     $q->execute();
    $res = [
      'message' => "Enregistrement réussi",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });
//Mise à jour d'une armoire

 $app->put('/armoire/update/{armoire_id}', function (Request $request, Response $response) {
  
  $armoire_id= $request->getAttribute('armoire_id');
  $data = $request->getBody()->getContents();

  $armoire = json_decode($data, true);
  
  $armoire_id= $armoire['armoire_id'];
  $code_armoire=$armoire['code_armoire'];
  $description= $armoire['description'];
  $compagnie_id=$armoire['compagnie_id'];
   
  $sql = "UPDATE armoire 
  SET armoire_id = :armoire_id,code_armoire = :code_armoire,description = :descriptions, compagnie_id = :compagnie_id
  WHERE armoire_id = :armoire_id";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
    $q->bindParam(':armoire_id',$armoire_id);
    $q->bindParam(':code_armoire',$code_armoire);
    $q->bindParam(':compagnie_id',$compagnie_id);
    $q->bindParam(':descriptions',$description);
    $q->execute();
    
    $res = [
      'message' => "Update successfully",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });

 //suppression d'une armoire

 $app->delete('/armoire/delete/{armoire_id}', function (Request $request, Response $response, array $args) {
  $id = $args['armoire_id'];
  $request->getAttribute('armoire_id');
  $sql = "DELETE FROM armoire WHERE armoire_id = ". $id;
   
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
    $q->execute();
    
    $res = [
      'message' => "Suppression réussie",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });


 //GESTION CLASSEUR

//lister des classeurs
$app->get('/classeur/all', function (Request $request, Response $response,array $args) {
  $sql = "SELECT * FROM classeur";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $compagnie = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    $response->getBody()->write(json_encode($compagnie));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });

//recuperer un enregistrement d'un classeur
$app->get('/classeur/one/{classeur_id}', function (Request $request, Response $response,array $args) {
  $id = $args['classeur_id'];
  $request->getAttribute('classeur_id');
  $sql = "SELECT * FROM classeur WHERE classeur_id=".$id;
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $compagnie = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    $response->getBody()->write(json_encode($compagnie));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });

//Inserer un classeur
 $app->post('/classeur/add', function (Request $request, Response $response) {
  $data = $request->getBody()->getContents();
  $classeur = json_decode($data, true);
  $code_classeur= $classeur['code_classeur'];
  $armoire_id= $classeur['armoire_id'];
  $sql = "INSERT INTO classeur(code_classeur,armoire_id) VALUES (:code_classeur,:armoire_id)";
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
    $q->bindParam(':code_classeur',$code_classeur);
    $q->bindParam(':armoire_id',$armoire_id);
     $q->execute();
    $res = [
      'message' => "Enregistrement réussi",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });
//Mise à jour d'un classeur

 $app->put('/classeur/update/{classeur_id}', function (Request $request, Response $response) {
  
  $classeur_id= $request->getAttribute('classeur_id');
  $data = $request->getBody()->getContents();
  $classeur = json_decode($data, true);
  $classeur_id=$classeur['classeur_id'];
  $code_classeur= $classeur['code_classeur'];
  $armoire_id=$classeur['armoire_id'];
   
  $sql = "UPDATE classeur
  SET classeur_id = :classeur_id, code_classeur = :code_classeur,armoire_id = :armoire_id
  WHERE classeur_id = :classeur_id";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
    $q->bindParam(':classeur_id', $classeur_id);
    $q->bindParam(':code_classeur',$code_classeur);
    $q->bindParam(':armoire_id',$armoire_id);
   
    $q->execute();
    
    $res = [
      'message' => "Update successfully",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });

 //suppression d'un classeur

 $app->delete('/classeur/delete/{classeur_id}', function (Request $request, Response $response, array $args) {
  $id = $args['classeur_id'];
  $request->getAttribute('classeur_id');
  $sql = "DELETE FROM classeur WHERE classeur_id= ". $id;
 
  try {
    $db = new Db();
    $conn = $db->connect();
    
    $q = $conn->prepare($sql);
    $q->execute();
    
    $res = [
      'message' => "Suppression réussie",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });


//GESTION DOSSIER

//lister des dossiers
$app->get('/dossier/all', function (Request $request, Response $response,array $args) {
  $sql = "SELECT * FROM dossier";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $compagnie = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    $response->getBody()->write(json_encode($compagnie));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });
//




//Emplacement d'un dossier
$app->get('/dossier/item/{dossier_id}', function (Request $request, Response $response,array $args) {
  $dossier_id = $args['dossier_id'];
  $request->getAttribute('dossier_id');
  $sql ="SELECT dossier_id,code_dossier,code_armoire,code_classeur,nom 
  FROM armoire AS AR INNER JOIN classeur AS CA ON CA.armoire_id=AR.armoire_id
  INNER JOIN dossier AS D ON D.classeur_id=CA.classeur_id
  INNER JOIN Traitement AS TA ON TA.dossier_id=D.dossier_id WHERE dossier_id=".$dossier_id;
  
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $compagnie = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    $response->getBody()->write(json_encode($compagnie));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });

//afficher un dossier


$app->get('/dossier/one/{dossier_id}', function (Request $request, Response $response,array $args) {
     $id = $args['dossier_id'];
    $request->getAttribute('dossier_id');
  
  $sql = "SELECT * FROM dossier WHERE dossier_id=".$id; 
    
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $dossier = $stmt->fetchAll(PDO::FETCH_OBJ);

    $db = null;
    $response->getBody()->write(json_encode($dossier));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });

//Inserer un dossier
 $app->post('/dossier/add', function (Request $request, Response $response) {
  $data = $request->getBody()->getContents();
  $dossier = json_decode($data, true);
  $code_dossier= $dossier['code_dossier'];
  $nom= $dossier['nom'];
  $description= $dossier['descriptions'];
  $classeur_id= $dossier['classeur_id'];
  $sql = "INSERT INTO dossier(code_dossier,nom,description,classeur_id) VALUES (:code_dossier,:nom,:descriptions, :classeur_id)";
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
   
    $q->bindParam(':code_dossier',$code_dossier);
    $q->bindParam(':nom',$nom);
    $q->bindParam(':descriptions',$description);
    $q->bindParam(':classeur_id',$classeur_id);
     $q->execute();
    $res = [
      'message' => "Enregistrement réussi",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });
//Mise à jour d'un dossier

 $app->put('/dossier/update/{dossier_id}', function (Request $request, Response $response) {
  
  $dossier_id= $request->getAttribute('dossier_id');
  $data = $request->getBody()->getContents();

  $dossier = json_decode($data, true);
  $dossier_id=$dossier['dossier_id'];
  $code_dossier= $dossier['code_dossier'];
  $description=$dossier['description'];
  $classeur_id=$dossier['classeur_id'];
   
  $sql = "UPDATE dossier 
  SET dossier_id = :dossier_id, code_dossier = :code_dossier,description = :descriptions, classeur_id = :classeur_id
  WHERE dossier_id = :dossier_id";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
    $q->bindParam(':dossier_id', $dossier_id);
    $q->bindParam(':code_dossier',$code_dossier);
    $q->bindParam(':descriptions',$description);
    $q->bindParam(':classeur_id',$classeur_id);
   
    $q->execute();
    
    $res = [
      'message' => "Update successfully",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });

 //suppression d'un dossier

 $app->delete('/dossier/delete/{dossier_id}', function (Request $request, Response $response, array $args) {
  $id = $args['dossier_id'];
  $request->getAttribute('dossier_id');
  $sql = "DELETE FROM dossier WHERE dossier_id= ". $id;
 
  try {
    $db = new Db();
    $conn = $db->connect();
    
    $q = $conn->prepare($sql);
    $q->execute();
    
    $res = [
      'message' => "Suppression réussie",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });


//  traitements des dossiers

//lister des dossiers
$app->get('/traitement/all', function (Request $request, Response $response,array $args) {
  $sql = "SELECT * FROM traitement";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $compagnie = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    $response->getBody()->write(json_encode($compagnie));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });

//Inserer un dossier traité
 $app->post('/traitement/add', function (Request $request, Response $response) {
  $data = $request->getBody()->getContents();
  $traitement = json_decode($data, true);
   
  $date_traitement= $traitement['date_traitement'];
  $dossier_id= $traitement['dossier_id'];
  $utilisateur_id= $traitement['utilisateur_id'];
  $sql = "INSERT INTO traitement(date_traitement,dossier_id,utilisateur_id) VALUES (:date_traitement,:dossier_id,:utilisateur_id)";
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
    $q->bindParam(':date_traitement',$date_traitement);
    $q->bindParam(':dossier_id',$dossier_id);
    $q->bindParam(':utilisateur_id',$utilisateur_id);
    $q->execute();
    $res = [
      'message' => "Enregistrement réussi",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });
//Mise à jour d'un dossier traitement

 $app->put('/traitement/update/{traitement_id}', function (Request $request, Response $response) {
  
  $traitement_id= $request->getAttribute('traitement_id');
  $data = $request->getBody()->getContents();

  $traitement = json_decode($data, true);

  $traitement_id=$traitement['traitement_id'];
  $date_traitement=$traitement['date_traitement'];
  $dossier_id= $traitement['dossier_id'];
  $utilisateur_id=$traitement['utilisateur_id'];
  
   
  $sql = "UPDATE traitement
  SET traitement_id = :traitement_id, date_traitement = :date_traitement,dossier_id = :dossier_id,utilisateur_id = :utilisateur_id
  WHERE traitement_id = :traitement_id";
 
  try {
    $db = new Db();
    $conn = $db->connect();
    $q = $conn->prepare($sql);
    $q->bindParam(':traitement_id', $traitement_id);
    $q->bindParam(':date_traitement',$date_traitement);
    $q->bindParam(':dossier_id',$dossier_id);
    $q->bindParam(':utilisateur_id',$utilisateur_id);
   
    $q->execute();
    
    $res = [
      'message' => "Update successfully",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });

 //suppression d'un traitement

 $app->delete('/traitement/delete/{traitement_id}', function (Request $request, Response $response, array $args) {
  $id = $args['traitement_id'];
  $request->getAttribute('traitement_id');
  $sql = "DELETE FROM traitement WHERE traitement_id= ". $id;
 
  try {
    $db = new Db();
    $conn = $db->connect();
    
    $q = $conn->prepare($sql);
    $q->execute();
    
    $res = [
      'message' => "Suppression réussie",
      "status" => 200
    ];
    $db = null;
   
    $response->getBody()->write(json_encode($res));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);

  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });
 
//Rechercher un dossier 

$app->get('/traitement/item/{traitement_id}', function (Request $request, Response $response,array $args) {
  $id = $args['traitement_id'];
  $request->getAttribute('traitement_id');
  $sql = "SELECT * FROM traitement,dossier WHERE traitement.dossier_id= dossier.dossier_id AND traitement_id=".$id;
  // $sql = "SELECT * FROM traitement INNER JOIN dossier ON traitement.dossier_id= dossier.dossier_id ";
  
  try {
    $db = new Db();
    $conn = $db->connect();
    $stmt = $conn->query($sql);
    $compagnie = $stmt->fetchAll(PDO::FETCH_OBJ);
    $db = null;
    $response->getBody()->write(json_encode($compagnie));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(200);
  } catch (PDOException $e) {
    $error = array(
      "message" => $e->getMessage()
    );
 
    $response->getBody()->write(json_encode($error));
    return $response
      ->withHeader('content-type', 'application/json')
      ->withStatus(407);
  }
 });


$app->run();
 