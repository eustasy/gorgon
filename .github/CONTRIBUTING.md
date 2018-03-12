# Contributing

We love pull requests from everyone. Check out our [open issues](https://github.com/eustasy/gorgon/issues), partically anything tagged as Bytesize, for things you can get to work on. By participating in this project, you agree to abide by the project [code of conduct](https://github.com/eustasy/gorgon/blob/master/.github/CODE_OF_CONDUCT.md).

## Setting Up

See the [Installation Guide](https://github.com/eustasy/gorgon#install-guide) for requirements and setup.

## Code Style
### PHP

 - Tabular indentation, spaces for spacing.
 - Four stroke headers to large sections.
 - Remove trailing whitespaces.
 - IE 11+ Compatibility.
 - `require_once` any other file
 - Don't close PHP tags on PHP only files

```php
<?php

////	Large Header
// Description of what this secion
// does and does not do.
// Especially any caveats.

$hello = array(
	'key'       => 1,
	'other_key' => 2,
	'final_key' => 3
);

require_once $settings['functions'].'function.super_function.php';
$result = super_function($hello, $world);

if ( $result ) {
	...
} else if (
	!$result &&
	$earlier_result
) {
	...
} else {
	...
}
```

### HTML
 - Include `alt` attribute for all images
 - Include `title` attribute for all links
 - Close all your tags properly

### CSS
 - Try to use classes instead of IDs unless things are absolutely unique
 - One selector per line
 - Care with fallbacks and browsers compatibilities
```css
.class {
    color: #fefe89;
    font-size: 1.1rem;
}

.second-class,
.third-class {
    backgound-color: white;
}
```

## Git

We recommend forking the repository, and then cloning your new repo.

    git clone git@github.com:your-username/bubbly.git

Make changes and commit them in your fork, preferably on a nicely named branch with descriptive commit messages.

### Make a new branch and push it to GitHub.
```bash
git checkout -b fix-issue-number
git push -u origin fix-issue-number
```

### Merge from master
```bash
git checkout fix-issue-number
git merge master
```

## Contact Points

For any security concerns arising from the state of this repository, please contact [security@eustasy.org](mailto:security@eustasy.org)

For anything else, please [file an issue](https://github.com/eustasy/gorgon/issues/new) on this repository.
