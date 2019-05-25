<?php

namespace PeteKlein\Performant\Generators;

class Scaffold
{
    /** an array represents a directory */
    private $fileStructure;
    
    public function __construct()
    {
        $this->fileStructure = [
            'Menu' => [
                'PrimaryMenu' => [
                    'PrimaryMenu.php'
                ]
            ],
            'PostTypes' => [
                'Posts' => [
                    'Post.php',
                    'PostCollection.php',
                    'PostDetail.php'
                ],
                'Pages' => [
                    'Page.php',
                    'PageCollection.php',
                    'PageDetail.php'
                ]
            ],
            'Taxonomies' => [
                'Categories' => [
                    'Category.php'
                ],
                'Tags' => [
                    'Tag.php'
                ]
            ],
            'UserRoles' => [
                'Administrator' => [
                    'Administrator.php',
                    'AdministratorCollection.php',
                    'AdministratorDetail.php'
                ],
                'Contributor' => [
                    'Contributor.php',
                    'ContributorCollection.php',
                    'ContributorDetail.php'
                ],
                'Editor' => [
                    'Editor.php',
                    'EditorCollection.php',
                    'EditorDetail.php'
                ],
                'Subscriber' => [
                    'Subscriber.php',
                    'SubscriberCollection.php',
                    'SubscriberDetail.php'
                ]
            ]
        ];
    }
}
