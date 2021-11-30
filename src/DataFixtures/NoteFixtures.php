<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\User;
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
        $users = [];
        for ($i = 0; $i < 3; $i++) {
            $user = new User("user $i");
            $manager->persist($user);

            $users[] = $user;
        }

        $categories = [];

        for ($i = 0; $i < 3; $i++) {
            $category = new Category($this->categoryTitles[$i], $users[$i]);
            $manager->persist($category);

            $categories[] = $category;
        }

        for ($i = 1; $i <= 9; $i++) {
            $category = $categories[random_int(0, 2)];
            $note = new Note(
                'Some note ' . $i,
                'Lorem ipsum ' . $i,
                $category,
                $category->getUser()
            );
            $manager->persist($note);
        }

        $manager->flush();
    }
}
