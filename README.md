# 🍋 Lemon

Lemon is dead simple php micro framework.\
Latest version: 2.6.4\
Documentation: https://github.com/Lemon-Framework/docs 

# Why?

Lemon started as project for learning php and Laravel. Main concept was to create tool for php developers that will have syntax similar to Laravel, but way simpler to setup.

Because of its simplicity, Lemon is perfect for beginners, because you can fit entire web page into one file.

# Installation

Installation is provided via composer, using this curl in your project folder:\
`curl -s 'https://raw.githubusercontent.com/Lemon-Framework/ProjectBuilder/master/builder' | bash`\
This is directory structure that you get
```
your_project/
├── vendor/
├── public/
│   ├── index.php
│   └── .htaccess
├── lemonade
└── composer.json

```
If you want to build bigger starting app, type `php lemonade build type:project`

# Contributing

Any contributions that don't rewrite whole framework are welcomend. But please use latest develop branch. (Currently [v3-develop](https://github.com/Lemon-Framework/Lemon/tree/v3-develop))
