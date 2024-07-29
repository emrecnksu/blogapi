Setup

Installation of the project and how to run it are explained in the README file within the project files.

TR

Projenin kurulumu ve nasıl çalıştırılacağı, proje dosyaları içerisindeki README dosyasında anlatılmıştır.

ENG:

First of all, since we will use git commands to pull the project from Github, Git Bash is installed. You can access this link to install according to the operating system: https://git-scm.com/downloads

Steps

Cloning This Repository Clone this GitHub repository to your local computer:
https://github.com/emrecnksu/blogapiofproject.git

Entering the Project Directory After cloning, you need to type the following command to enter the project folder in the new command line:
cd blogapiofproject

Creating and Installing a Project with Laravel: Install the composer you need to run the Laravel project with the following command:
composer install

OR;

You can create new Laravel projects by installing the Laravel installer globally via Composer:

composer global require laravel/installer

laravel new blogapiofproject

After following these steps, you need to enter the path where the file is located:

cd blogapiofproject
Configuring the required files: Once the installation is complete, we will add the '.env' file, which contains the configuration settings for the project and contains sensitive information such as database connections.
To do this, we will copy the sample configuration settings in the '.env.example' file, which comes automatically when the project is installed, to the .env file.

To do this, use the command: cp .env.example .env

For security purposes, run this command to create a new application key: php artisan key:generate

In Laravel, the storage/app/public directory is usually used to store files uploaded by users.

However, these files cannot be accessed directly via URL. Therefore, we want to create a symlink to the public/storage directory and thus save the files in the storage/app/public directory. To make it accessible via web browsers, the following command is used:

php artisan storage:link

Then run 'php artisan migrate:fresh --seed' command. This command resets the database and initializes it to run the new project.

Docker Installation First, make sure Docker is installed. You can install Docker using a tool like Docker Desktop. You can refer to the official Docker documentation for installation: https://www.docker.com/products/docker-desktop/

Creating Dockerfile and yml files: In order for the project to run and communicate with Docker, you need to create Dockerfile and docker-compose.yml files and edit their contents according to the project.

Launching the Application with Docker Compose In order to start your project via the docker application you have installed, you need to install it via docker.

Run the following command to start the application using Docker Compose:

docker-compose up -d

After creating the Docker files and getting the project up and running, different packages need to be installed in order to perform project.

bezhansalleh/filament-shield -> To install;
composer require bezhansalleh/filament-shield

filament/filament -> To install;
composer require filament/filament


TR:

Öncelikle projeyi Github'dan çekmek için git komutlarını kullanacağımızdan Git Bash kurulur. İşletim sistemine göre kurulum yapmak için bu bağlantıya erişebilirsiniz: https://git-scm.com/downloads

Adımlar

Bu Depoyu Klonlama Bu GitHub deposunu yerel bilgisayarınıza klonlayın:
https://github.com/emrecnksu/blogapiofproject.git

Proje Dizinine Girme Klonlamadan sonra, yeni komut satırına proje klasörüne girmek için aşağıdaki komutu yazmanız gerekir:
cd blogapiofproject

Laravel ile Bir Proje Oluşturma ve Yükleme: Composer'ı yükleyin Laravel projesini aşağıdaki komutla çalıştırmanız gerekir:
composer install

VEYA;

Laravel yükleyicisini Composer aracılığıyla global olarak yükleyerek yeni Laravel projeleri oluşturabilirsiniz:

composer global require laravel/installer

laravel new blogapiofproject

Bu adımları izledikten sonra dosyanın bulunduğu yolu girmeniz gerekir:

cd blogapiofproject
Gerekli dosyaları yapılandırma: Kurulum tamamlandıktan sonra, proje için yapılandırma ayarlarını ve veritabanı bağlantıları gibi hassas bilgileri içeren '.env' dosyasını ekleyeceğiz.
Bunu yapmak için, proje yüklendiğinde otomatik olarak gelen '.env.example' dosyasındaki örnek yapılandırma ayarlarını .env dosyasına kopyalayacağız.

Bunu yapmak için şu komutu kullanın: cp .env.example .env

Güvenlik amaçlı, yeni bir uygulama anahtarı oluşturmak için şu komutu çalıştırın: php artisan key:generate

Laravel'de, storage/app/public dizini genellikle kullanıcılar tarafından yüklenen dosyaları depolamak için kullanılır.

Ancak bu dosyalara doğrudan URL üzerinden erişilemez. Bu nedenle, public/storage dizinine bir sembolik bağlantı oluşturmak ve böylece dosyaları storage/app/public dizinine kaydetmek istiyoruz. Web tarayıcıları üzerinden erişilebilir hale getirmek için aşağıdaki komut kullanılır:

php artisan storage:link

Ardından 'php artisan migrate:fresh --seed' komutunu çalıştırın. Bu komut veritabanını sıfırlar ve yeni projeyi çalıştırmak üzere başlatır.

Docker Kurulumu Öncelikle Docker'ın kurulu olduğundan emin olun. Docker Desktop gibi bir araç kullanarak Docker'ı kurabilirsiniz. Kurulum için resmi Docker belgelerine başvurabilirsiniz: https://www.docker.com/products/docker-desktop/

Dockerfile ve yml dosyaları oluşturma: Projenin çalışması ve Docker ile iletişim kurması için Dockerfile ve docker-compose.yml dosyalarını oluşturmanız ve içeriklerini projeye göre düzenlemeniz gerekir.

Docker Compose ile Uygulamayı Başlatma Yüklediğiniz docker uygulaması üzerinden projenizi başlatmak için, onu docker üzerinden yüklemeniz gerekir.

Uygulamayı Docker Compose kullanarak başlatmak için aşağıdaki komutu çalıştırın:

docker-compose up -d

Docker dosyalarını oluşturduktan ve projeyi çalışır hale getirdikten sonra, projeyi gerçekleştirmek için farklı paketlerin yüklenmesi gerekir.

bezhansalleh/filament-shield -> Yüklemek için;
composer require bezhansalleh/filament-shield

filament/filament -> Yüklemek için;
composer require filament/filament