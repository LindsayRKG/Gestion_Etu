1- Modification de la database dans Classes/Database.php 
-ligne 146-152 remplacer dans le fichier lister_ver.php ajouter le code ci <td>
               <td>
               <a href="modifer_versement.php?matricule=<?= htmlspecialchars($versement['matricule_etudiant']); ?>" class="btn btn-primary">Modifier</a>

               <a href="supprimer_versement.php?matricule=<?= htmlspecialchars($versement['matricule_etudiant']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce versement ?');">Supprimer</a>
                </td>
  -
2- modification du lien css dans dashadmin.php en "assets/css/style.css"
3 modification de l'adresse email et du password dans Classes/Etudiants et Classes/Versements 
 reste a faire, 
 -bulletins avec signature
 -partie de gestions des bulletins ( affichage des noms et affichage du bulletins )
 - partie etudiants ( il y'a deja le dashboard )
 - Le groupe 7
 - faire la video et le rapport
 
