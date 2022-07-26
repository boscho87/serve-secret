gitPush=/usr/local/bin/push
gitDeleteBranches=/usr/local/bin/delete-branches
docIndexBin=/usr/local/bin/doc-index
docFileIndex=/usr/local/bin/doc-file-index
gitAddCommit=/usr/local/bin/commit
helpFile=/usr/local/bin/dev-tools-help

install: install-git-push install-git-delete-branches install-doc-index install-doc-file-index h2g-alias dev-tools-help info
info:
	echo "Installation successfully executed"
	dev-tools-help
install-git-push:
	cp ./src/push.sh $(gitPush)
	chown root $(gitPush)
	chgrp root $(gitPush)
	chmod 677 $(gitPush)
install-git-delete-branches:
	cp ./src/git-add-commit.sh $(gitAddCommit)
	chown root $(gitAddCommit)
	chgrp root $(gitAddCommit)
	chmod 677 $(gitAddCommit)
install-git-commit:
	cp ./src/git-delete-branches.sh $(gitDeleteBranches)
	chown root $(gitDeleteBranches)
	chgrp root $(gitDeleteBranches)
	chmod 677 $(gitDeleteBranches)
install-doc-index:
	cp ./src/doc_toc.sh $(docIndexBin)
	chown root $(docIndexBin)
	chgrp root $(docIndexBin)
	chmod 677 $(docIndexBin)
install-doc-file-index:
	cp ./src/file_toc.sh $(docFileIndex)
	chown root $(docFileIndex)
	chgrp root $(docFileIndex)
	chmod 677 $(docFileIndex)
h2g-alias:
	./src/install-alias.sh
dev-tools-help:
	cp ./src/help.sh $(helpFile)
	chown root $(helpFile)
	chgrp root $(helpFile)
	chmod 677 $(helpFile)

