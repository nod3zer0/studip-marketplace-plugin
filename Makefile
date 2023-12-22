
make:
	rm marketplace.zip; zip -r marketplace controllers/* views/* migrations/* classes/* models/* plugin.manifest Marketplace.class.php bootstrap.inc.php
