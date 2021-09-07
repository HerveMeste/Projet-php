<?php
    $pdo = require 'database/database.php';
class ArticleDB{

    private PDOStatement $statementCreateOne;
    private PDOStatement $statementUpdateOne;
    private PDOStatement $statementDeleteOne;
    private PDOStatement $statementReadOne;
    private PDOStatement $statementReadAll;
    private PDOStatement $statementReadUserAll;

    public function __construct(private PDO $pdo)
    {

        $this->statementUpdateOne = $pdo->prepare('
        UPDATE article
        SET
            title = :title,
            category = :category,
            content = :content,
            image = :image,
            author = :author
        WHERE id=:id');

        $this->statementCreateOne = $pdo->prepare('
        INSERT INTO article(
                            title,
                            category,
                            content,
                            image,
                            author
        )VALUES (
                 :title,
                 :category,
                 :content,
                 :image,
                 :author
        )');
        $this->statementReadOne = $pdo->prepare('SELECT article.*, user.firstname, user.lastname FROM article LEFT JOIN user  ON user.id = article.author WHERE article.id=:id');
        $this->statementReadAll = $pdo->prepare('SELECT article.*, user.firstname, user.lastname FROM article LEFT JOIN user ON user.id = article.author');
        $this->statementDeleteOne = $pdo->prepare('DELETE FROM article WHERE id=?');
        $this->statementReadUserAll = $pdo->prepare('SELECT * FROM article WHERE author=:authorId');
    }

    public function fetchAll():array{
        $this->statementReadAll->execute();
        return $this->statementReadAll->fetchAll();

    }
    public function fetchOne(int $id):array{
        $this->statementReadOne->bindValue('id',$id);
        $this->statementReadOne->execute();
        return $this->statementReadOne->fetch();


    }
    public function deleteOne(int $id):string{
//        $this->statementDeleteOne->bindValue('id',$id);
        $this->statementDeleteOne->execute([$id]);
        return $id;
    }
    public function createOne($article):array{
        $this->statementCreateOne->bindValue(':title',$article['title']);
        $this->statementCreateOne->bindValue(':image',$article['image']);
        $this->statementCreateOne->bindValue(':category',$article['category']);
        $this->statementCreateOne->bindValue(':content',$article['content']);
        $this->statementCreateOne->bindValue(':author',$article['author']);
        $this->statementCreateOne->execute();
        return $this->fetchOne($this->pdo->lastInsertId());

    }
    public function updateOne($article):array{
        $this->statementUpdateOne->bindValue(':title',$article['title']);
        $this->statementUpdateOne->bindValue(':image',$article['image']);
        $this->statementUpdateOne->bindValue(':category',$article['category']);
        $this->statementUpdateOne->bindValue(':content',$article['content']);
        $this->statementUpdateOne->bindValue(':author',$article['author']);
        $this->statementUpdateOne->bindValue(':id',$article['id']);
        $this->statementUpdateOne->execute();
        return $article;

    }
    public function fetchUserArticle(string $authorId):array {
        $this->statementReadUserAll->bindValue('authorId', $authorId);
        $this->statementReadUserAll->execute();
        return $this->statementReadUserAll->fetchAll();
    }

}

    return new ArticleDB($pdo);
