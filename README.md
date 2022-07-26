# dev-tools
dev-tools

Simple shell scripts that should help the h2g-devs to Create Doc Index, Simplify Git Commands and more

<!-- TOC -->
- [dev-tools](#dev-tools)
  - [Requirements](#requirements)
  - [Development](#development)
  - [Install](#install)
  - [Update](#update)
  - [Scripts](#scripts)
    - [`dev-tools-help`](#dev-tools-help)
    - [`doc-index`](#doc-index)
    - [`doc-file-index`](#doc-file-index)
    - [`delete-branches`](#delete-branches)
    - [`push`](#push)
    - [`commit`](#commit)
  - [Aliases](#aliases)
    - [`gh`](#gh)

<!-- /TOC -->

### Requirements

- ***git***, some scripts are helpers for git, and to install the shell scripts on your system, git is also the simplest version
- ***make***, to install the libraries, `make` is needed to install the makefile `sudo apt install make` `yay -S make` 

### Development

0. Checkout the `main` branch
1. Add your code
- (a) Add a Script:

if you want to add a Script:  
- create your shell script in the `src` directory
- add the installation routine to the makefile (like the already existing scripts)

- or (b) add an Alias  
if you want to add a new alias, just add the alias to the [.h2g-alias](src/.h2g-alias)

2. Update the Readme file. Update the Readme file (don't forget the TOC)
3. Update the Changelog
4. Create a Pull Request on [GitHub](https://github.com/h2ginternetagentur/dev-tools/pulls) 


### Install

```bash
 git clone https://github.com/h2ginternetagentur/dev-tools.git && cd dev-tools &&  sudo make install
```

You also can install only some scripts, take a look in the [makefile](./makefile). e.g. sudo make install install-git-push

### Update

Update if you already have the

```bash
cd dev-tool && git pull origin main && sudo make install
```

## Scripts

### `dev-tools-help`

Show information about all this scripts

### `doc-index`

Fetch all .md files in a Directory and Create a File named 000-TOC.md with the index of all files found in the given directory

Usage:
```bash
doc-index _doc
```

### `doc-file-index`

Create an index for a given File, the Doc denn needs to be copy/past into the file.

Usage: 
```bash
doc-file-index Readme.md
```

### `delete-branches`

Delete all but the Current git branch in a Project (does not execute git prune). 

Usage:
```bash
delete-branches 
# will maybe often be used to cleanup local repos like:
delete-branches &&  git remote prune origin
```

### `push`

Instead of `git push orign local/branch` you can just write `push` into your console

Usage:
```bash
push
```

### `commit`

Write just `commit "My Message"` instead of `git add . && git commit -m "My Message"`

Usage:
```bash
commit "my new commit message #13"
```


## Aliases

Some Tools ar not scripts but, simple aliases. The aliases are stored in ~/.h2g-alias, to check if the installation had work try `h2g-test-alias`. 

For `bash` and `zsh` its should work out of the box.

***Problems?***  
If this not is working, check if the file ~/.h2g-alias exits `test -e ~/.h2g-alias && echo $?` if the output is `0` the file exists. If the file exists you need to add `source ~/.h2g-alias` in your `.bash_profile` file. 


### `gh`

Search in the history with grep

Usage:
```bash 
gh "rebase"
```
