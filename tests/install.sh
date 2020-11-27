#!/usr/bin/env bash
clear
echo "*************************************************************************"
echo "*                                                                       *"
echo "*      Welcome to the PinkCrab WooCommerce PHPUnit install script.      *"
echo "*                                                                       *"
echo "*************************************************************************"
echo "* Please enter your DBName                                              *"
read -p "* > "   DBName
echo "* Please enter your DBUserName                                          *"
read -p "* > "   DBUserName
echo "* Please enter your DBPassword                                          *"
read -p "* > "   DBPassword
echo "* Please enter your DBHost                                              *"
read -p "* > "   DBHost


echo "*************************************************************************"
echo "*                                                                       *"
echo "*                 Installing WooCommerce testing suite                  *"
echo "*                                                                       *"
echo "*************************************************************************"
echo "*                                                                       *"
echo "* Cloning latest WooCommerce Repo...                                    *"
echo "*                                                                       *"
git clone https://github.com/woocommerce/woocommerce.git woo



echo "*                                                                       *"
echo "* Removing GIT from WC Test Repo...                                     *"
rm -rf woo/.git


echo "*                                                                       *"
echo "* Setting up WooCommerce WP_Unit suites                                 *"
echo "*                                                                       *"
if [[ -z "$DBPassword" ]]; then
   bash woo/tests/bin/install.sh $DBName $DBUserName "" $DBHost
else
   bash woo/tests/bin/install.sh $DBName $DBUserName $DBPassword $DBHost
fi

echo "*                                                                       *"
echo "* Running composer install....                                          *"
echo "*                                                                       *"
cd woo && composer install && cd ..


echo "*                                                                       *"
echo "* Linking local bootstrap file                                          *"
echo "*                                                                       *"
filename='woo/tests/legacy/bootstrap.php'
sed -i '/install WC./a \        include dirname( __FILE__, 4) . "/bootstrap.php";' $filename 


echo "*************************************************************************"
echo "*                                                                       *"
echo "*                               FINISHED                                *"
echo "*                                                                       *"
echo "*************************************************************************"
echo "*                                                                       *"
echo "* Your test suite is now installed                                      *"
echo "*                                                                       *"
echo "* Place all your tests in the unit-test directory                       *"
echo "*                                                                       *"
echo "* Type 'phpunit' to run the tests.                                      *"
echo "*                                                                       *"
echo "* The whole WC test suite can be run, by uncommenting in phpunit.xml    *"
echo "*                                                                       *"
echo "*************************************************************************"
echo "*                                                                       *"
echo "*                         NOW TEST, TEST, TEST!                         *"
echo "*                                                                       *"
echo "*************************************************************************"


