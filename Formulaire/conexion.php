<?php
//nous allons demarer la session avant tout.

    session_start() ;

if (isset($_POST['button-valider'])) { //si on clique sur '', alors:

    //Nous allons vérifier les info
    if (isset($_POST['button-valider'])) {
        # code...
    
        if (isset($_POST['email']) && isset($_POST['mdp'])) { //on verifie
            $email = $_POST['email'] ;
            $mdp = $_POST['mdp'] ;
            $erreur = "";
            //nous verifion
            //connection a la bdd
            $nom_serveur = "localhost";
            $utilisateur = "root";
            $mot_de_passe ="";
            $nom_base_données= "formulaire site fond d'ecran";
            $con = mysqli_connect($nom_serveur , $utilisateur ,$mot_de_passe , $nom_base_données);
            //requete
            $req = mysqli_query($con , "SELECT * FROM utilisateurs WHERE email ='$email' AND mdp ='$mdp' ");
            $num_ligne = mysqli_num_rows($req) ; //compter le nombre de ligne ayant la requette sql
            if ($num_ligne > 0){
                header("location: bienvenue.php");
                //Nous allons créer une variable session qui va contenir l'email de l'utilisateur
                $_SESSION['email'] = $email ;
            }else {
                $erreur = "Adresse mail ou Mot de passe incorectes !";
            }
    
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de connection</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <section>
        <?php 
        if (isset($erreur)) { //si la variable erreur existe en affiche le contenu
           echo $erreur;
        }
        ?>
    <!-- From Uiverse.io by Smit-Prajapati --> 
<div class="container">
    <div class="heading">Sign In</div>
    <form action="" class="form" method="post">
      <input required="" class="input" type="email" name="email" id="email" placeholder="E-mail">
      <input required="" class="input" type="password" name="mot_de_passe" id="mot_de_passe" placeholder="Password">
      <input class="login-button" type="submit" value="Sign In" href="bienvenue.php" name="button-valider">
      
    </form>
    </div>
    <style>
        /* From Uiverse.io by Smit-Prajapati */ 
.container {
  max-width: 350px;
  background: #F8F9FD;
  background: linear-gradient(0deg, rgb(255, 255, 255) 0%, rgb(244, 247, 251) 100%);
  border-radius: 40px;
  padding: 25px 35px;
  border: 5px solid rgb(255, 255, 255);
  box-shadow: rgba(133, 189, 215, 0.8784313725) 0px 30px 30px -20px;
  margin: 20px;
  margin-left: auto;
  margin-right: auto;
  width: ...; /* largeur obligatoire pour être centré */
}

.heading {
  text-align: center;
  font-weight: 900;
  font-size: 30px;
  color: rgb(16, 137, 211);
}

.form {
  margin-top: 20px;
}

.form .input {
  width: 100%;
  background: white;
  border: none;
  padding: 15px 20px;
  border-radius: 20px;
  margin-top: 15px;
  box-shadow: #cff0ff 0px 10px 10px -5px;
  border-inline: 2px solid transparent;
}

.form .input::-moz-placeholder {
  color: rgb(170, 170, 170);
}

.form .input::placeholder {
  color: rgb(170, 170, 170);
}

.form .input:focus {
  outline: none;
  border-inline: 2px solid #12B1D1;
}

.form .forgot-password {
  display: block;
  margin-top: 10px;
  margin-left: 10px;
}

.form .forgot-password a {
  font-size: 11px;
  color: #0099ff;
  text-decoration: none;
}

.form .login-button {
  display: block;
  width: 100%;
  font-weight: bold;
  background: linear-gradient(45deg, rgb(16, 137, 211) 0%, rgb(18, 177, 209) 100%);
  color: white;
  padding-block: 15px;
  margin: 20px auto;
  border-radius: 20px;
  box-shadow: rgba(133, 189, 215, 0.8784313725) 0px 20px 10px -15px;
  border: none;
  transition: all 0.2s ease-in-out;
}

.form .login-button:hover {
  transform: scale(1.03);
  box-shadow: rgba(133, 189, 215, 0.8784313725) 0px 23px 10px -20px;
}

.form .login-button:active {
  transform: scale(0.95);
  box-shadow: rgba(133, 189, 215, 0.8784313725) 0px 15px 10px -10px;
}

.social-account-container {
  margin-top: 25px;
}

.social-account-container .title {
  display: block;
  text-align: center;
  font-size: 10px;
  color: rgb(170, 170, 170);
}

.social-account-container .social-accounts {
  width: 100%;
  display: flex;
  justify-content: center;
  gap: 15px;
  margin-top: 5px;
}

.social-account-container .social-accounts .social-button {
  background: linear-gradient(45deg, rgb(0, 0, 0) 0%, rgb(112, 112, 112) 100%);
  border: 5px solid white;
  padding: 5px;
  border-radius: 50%;
  width: 40px;
  aspect-ratio: 1;
  display: grid;
  place-content: center;
  box-shadow: rgba(133, 189, 215, 0.8784313725) 0px 12px 10px -8px;
  transition: all 0.2s ease-in-out;
}

.social-account-container .social-accounts .social-button .svg {
  fill: white;
  margin: auto;
}

.social-account-container .social-accounts .social-button:hover {
  transform: scale(1.2);
}

.social-account-container .social-accounts .social-button:active {
  transform: scale(0.9);
}

.agreement {
  display: block;
  text-align: center;
  margin-top: 15px;
}

.agreement a {
  text-decoration: none;
  color: #0099ff;
  font-size: 9px;
}
</style>
</body>
</html>