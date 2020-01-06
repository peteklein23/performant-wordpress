# Performant

An opinionated, developer-friendly, performance-oriented framework for building applications quickly with WordPress.

## Quick Start

Add the repository to your `composer.json` and require it:

```json
"repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/peteklein23/performant-wordpress"
    }
],
"require": {
    "peteklein/performant-wordpress": "dev-master"
},
```

Install the package with composer:

```sh
composer install

```

Create a Field Group:

```php
<?php

namespace MyProjectNamespace\FieldGroups;

use PeteKlein\Performant\Fields\FieldGroupBase;
use PeteKlein\Performant\Fields\CarbonFields\RichTextField;
use PeteKlein\Performant\Fields\CarbonFields\ImageField;
use PeteKlein\Performant\Fields\CarbonFields\FileField;
use PeteKlein\Performant\Fields\CarbonFields\TextField;
use PeteKlein\Performant\Fields\CarbonFields\TextAreaField;
use PeteKlein\Performant\Fields\CarbonFields\SelectField;
use PeteKlein\Performant\Fields\CarbonFields\RadioField;
use PeteKlein\Performant\Fields\CarbonFields\ColorField;
use PeteKlein\Performant\Fields\CarbonFields\DateField;
use PeteKlein\Performant\Fields\CarbonFields\DateTimeField;
use PeteKlein\Performant\Fields\CarbonFields\TimeField;
use PeteKlein\Performant\Fields\CarbonFields\OEmbedField;
use PeteKlein\Performant\Fields\CarbonFields\PostTypeField;

use MyProjectNamespace\PostTypes\ProjectType;

class ProjectInfo extends FieldGroupBase
{
    public function __construct()
    {
        $fields = [
            new PostTypeField(
                'related_projects',
                'Related Projects',
                [],
                ProjectType::POST_TYPE
            ),
            new TextField('employer', 'Employer', null, [
                'required' => true
            ]),
            new TextAreaField('text_area_test', 'Text Area Test', null, [
                'required' => true
            ]),
            new RichTextField('description', 'Description'),
            new SelectField('select_test', 'Select Test', null, [
                'options' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5
                ]
            ]),
            new RadioField('radio_test', 'Radio Test', null, [
                'options' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5
                ]
            ]),
            new ColorField('color_test', 'Color Test', '#FF0000', [
                'colors' => ['#FF0000', '#00FF00', '#0000FF'],
                'alpha_enabled' => true
            ]),
            new DateField('date_test', 'Date Test', null, [
                'picker_options' => [
                    'minDate' => '2020-01-01'
                ]
            ]),
            new DateTimeField('date_time_test', 'Date Time Test', null, [
                'picker_options' => [
                    'minDate' => '2020-01-01 12:00:00'
                ]
            ]),
            new TimeField('time_test', 'Time Test', null, [
                'picker_options' => [
                    'minDate' => '07:00:00'
                ]
            ]),
            new OEmbedField('oembed_test', 'oEmbed Test'),
            new ImageField('test_image', 'Test Image'),
            new FileField('test_file', 'Test File')
        ];

        parent::__construct('Project Info', $fields);
    }
}

```

Create a Taxonomy:

```php
<?php

namespace MyProjectNamespace\Taxonomies;

use PeteKlein\Performant\Taxonomies\TaxonomyBase;
use MyProjectNamespace\PostTypes\ProjectType;

class Skill extends TaxonomyBase
{
    const TAXONOMY = 'skill';

    public function __construct()
    {
        parent::__construct();
    }

    public function register(): void
    {
        $args = parent::getRegistrationArgs(
            parent::CATEGORY_TYPE,
            'Skill',
            'Skills'
        );
        $this->registerTaxonomy([ProjectType::POST_TYPE], $args);
    }
}

```

Create or set an existing Image Size

```php
<?php

namespace MyProjectNamespace\ImageSizes;

use PeteKlein\Performant\Images\ImageSizeBase;

class Large extends ImageSizeBase
{
    const SIZE = 'large';

    public function __construct()
    {
        parent::__construct(611, 611);
    }
}

```

Create a Post Type

```php
<?php

namespace MyProjectNamespace\PostTypes;

use PeteKlein\Performant\Fields\CarbonFields\CFContainer;
use PeteKlein\Performant\Fields\FieldGroupBase;
use PeteKlein\Performant\Posts\PostTypeBase;
use MyProjectNamespace\FieldGroups\ProjectInfo;
use MyProjectNamespace\ImageSizes\Large;
use MyProjectNamespace\Taxonomies\Skill;

class ProjectType extends PostTypeBase
{
    const POST_TYPE = 'project';

    public function __construct()
    {
        parent::__construct();

        $this->setTaxonomies([Skill::getInstance()]);
        $this->setFieldGroups([new ProjectInfo()]);
        $this->setFeaturedImageSizes([
            Large::getInstance()
        ]);
    }

    public function register()
    {
        $args = parent::getRegistrationArgs(
            parent::PUBLIC_TYPE,
            'Project',
            'Projects',
            'dashicons-awards'
        );

        $this->registerPostType($args);
    }

    public function createAdminContainer(FieldGroupBase $fieldGroup)
    {
        new CFContainer($fieldGroup, 'post_meta', 'post_type', self::POST_TYPE);
    }
}
```

Create a User Role:

```php
<?php

namespace MyProjectNamespace\UserRoles;

use PeteKlein\Performant\Fields\FieldGroupBase;
use PeteKlein\Performant\Fields\CarbonFields\CFContainer;
use PeteKlein\Performant\Users\UserRoleBase;

use MyProjectNamespace\FieldGroups\ProjectInfo;

class CreatorRole extends UserRoleBase
{
    const ROLE = 'creator';

    public function __construct()
    {
        parent::__construct('Creator', ['create' => true]);

        $this->setFieldGroups([new ProjectInfo()]);
    }

    public function createAdminContainer(FieldGroupBase $fieldGroup)
    {
        new CFContainer($fieldGroup, 'user_meta', 'user_role', self::ROLE);
    }
}

```

Tie it all together in a Theme file:

```php
<?php

namespace MyProjectNamespace;

use PeteKlein\Performant\PerformantTheme;
use MyProjectNamespace\UserRoles\CreatorRole;
use MyProjectNamespace\PostTypes\ProjectType;
use MyProjectNamespace\Taxonomies\Skill;
use MyProjectNamespace\ImageSizes\Large;

class MyProject extends PerformantTheme
{
    public function __construct()
    {
        \Carbon_Fields\Carbon_Fields::boot();
        parent::__construct();
    }

    public function registerUserRoles(): void
    {
        CreatorRole::getInstance()->register();
    }

    protected function registerImageSizes(): void
    {
        Large::getInstance()->register();
    }

    protected function registerTaxonomies()
    {
        Skill::getInstance()->register();
    }

    public function registerPostTypes()
    {
        ProjectType::getInstance()->register();
    }

    public function registerScripts()
    {
        wp_enqueue_script('jquery');
    }

    public function registerStyles()
    {
        wp_enqueue_style('my-website-style', get_stylesheet_uri());
    }
}

```

Include your theme file in `functions.php`:

```php
<?php

use MyProjectNamespace\MyProject;

require_once ABSPATH . '/vendor/autoload.php';

new MyProject();

```

Use it in a page or API endpoint (this example is in the template file `single-project.php`):

```php
<?php
global $post;

use MyProjectNamespace\PostTypes\ProjectType;
use PeteKlein\Performant\Posts\Post;

$projectType = ProjectType::getInstance();
$project = new Post($post->ID, $projectType);

get_header();

$projectData = $project->get();
$meta = $projectData['meta'];
$relatedProjectIds = $meta['related_projects'];
$relatedProjects = $projectType->listPostData($relatedProjectIds);
?>
<h3>This Project</h3>
<pre>
<?php var_dump($projectData); ?>
</pre>

<h3>Related Projects</h3>
<pre>
<?php var_dump($relatedProjects); ?>
</pre>

```
