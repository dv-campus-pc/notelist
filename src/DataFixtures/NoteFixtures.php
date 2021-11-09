<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Note;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class NoteFixtures extends Fixture
{
    private $categoryTitles = [
        'My Summer weekends',
        'My favorite books review',
        'My friends hobbies'
    ];

    public function load(ObjectManager $manager): void
    {
        $categories = [];

        for ($i = 0; $i < 3; $i++) {
            $category = new Category($this->categoryTitles[$i]);
            $manager->persist($category);

            $categories[] = $category;
        }

        for ($i = 1; $i <= 9; $i++) {
            $note = new Note(
                'Some note ' . $i,
                'Lorem ipsum ' . $i,
                $categories[random_int(0, 2)]
            );
            $manager->persist($note);
        }

        $manager->flush();
    }
}
