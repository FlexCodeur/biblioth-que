<?php
require __DIR__ . '/header.php';
require_once PATH_PROJECT . '/connect.php';

/* afficher tous les livres avec :
- Couverture du livre
- Titre du livre
- Prénom et nom de l'auteur
- Année d'édition
- Genre du livre
- Résumé du livre
- Lien vers la page du livre
- Pagination avec 6 livres par page

Tout l'affichage des livres devra être dans un container
*/

$req = $db->query("SELECT COUNT(id) AS count_book
	FROM livre
");
$result = $req->fetchObject();

$count_books = $result->count_book;

$per_page = 3;
$number_pages = ceil($count_books / $per_page);

if (isset($_GET['page']) && $_GET['page'] > 0 && $_GET['page'] <= $number_pages) {
    $current_page = $_GET['page'];
} else {
    $current_page = 1;
}

$req = $db->prepare("SELECT l.id, l.titre, l.resume, l.annee, l.auteur_id, l.genre_id, l.illustration_id, a.nom, a.prenom, g.libelle, i.file_name
FROM livre l
LEFT JOIN auteur a 
ON a.id = l.auteur_id
LEFT JOIN genre g
ON g.id = l.genre_id
LEFT JOIN illustration i
ON l.illustration_id = i.id
GROUP BY l.id
ORDER BY l.id ASC;
LIMIT :offset, :per_page
");

$offset = ($current_page - 1) * $per_page;

$req->bindValue(':offset', $offset, PDO::PARAM_INT);
$req->bindValue(':per_page', $per_page, PDO::PARAM_INT);
$req->execute();
$books = $req->fetchAll(PDO::FETCH_OBJ);
?>

<h1>Liste des livres</h1>
<main>

    <?php
    include PATH_PROJECT . '/views/pagination.php';
    foreach ($books as $book) :

        $id_book = $book->id;
    ?>
    <div class="container">
        <div class="content-items">
            <img src="<?php echo "../assets/img/{$book->file_name}"; ?>" alt="">
            <h3><?php echo sanitize_html($book->titre); ?></h3>
            <p>Auteur : <?php echo sanitize_html($book->prenom) . ' ' . sanitize_html($book->nom); ?></p>
            <p>Année de l'édition :<?php echo sanitize_html($book->annee); ?></p>
            <p>Genre : <?php echo sanitize_html($book->libelle); ?></p>
            <p>Résumé : <?php echo sanitize_html($book->resume); ?></p>
            <a href="<?php echo HOME_URL . 'views/books.php?id=' . $id_book; ?>">
                Voir la page du livre
                <?php echo sanitize_html($book->titre) ?>
            </a>
        </div>
    </div>
    <?php endforeach; ?>
</main>









<?php
include PATH_PROJECT . '/views/pagination.php';

require_once PATH_PROJECT . '/views/footer.php';