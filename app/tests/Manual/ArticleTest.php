<?php

namespace App\Tests;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ArticleTest extends KernelTestCase
{
    private $article;

    protected function setUp()
    {
        $this->article = new Article();

        $this->article->setAuthor('Sergey');
    }

    public function testAuthor()
    {
        $this->assertEquals('Sergey', $this->article->getAuthor());

        return 'Sergey';
    }

    /**
     * @depends testAuthor
     */
    public function testAuthor2($name)
    {
        $this->assertEquals($name, $this->article->getAuthor());
    }

    /**
     * @dataProvider authorProvider
     */
    public function testAuthor3($name)
    {
        $this->assertEquals($name, $this->article->getAuthor());
    }

    public function authorProvider()
    {
        return [
            ['Sergey']
        ];
    }
}