<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('email', 'emrecanblogsite@gmail.com')->first();

        $posts = [
            [
                'title' => 'Teknolojide Yapay Zekanın Geleceği',
                'content' => 'Yapay zeka, teknoloji alanında eşi benzeri görülmemiş yollarla dönüşüm sağlıyor. Görevlerin otomasyonundan karar verme süreçlerine kadar...',
                'image' => 'https://cdn.yemek.com/mnresize/1250/833/uploads/2022/03/tart-kalibinda-elmali-turta-hr.jpg',
                'category_id' => Category::where('name', 'Teknoloji')->first()->id,
                'user_id' => $adminUser->id,
                'tags' => ['AI,Teknoloji'],
                'status' => true,
                'start_date' => now(),
                'end_date' => null
            ],
            [
                'title' => 'Daha Sağlıklı Bir Yaşam İçin 10 İpucu',
                'content' => 'Sağlıklı bir yaşam tarzı benimsemek zor olabilir, ancak bu on ipucu ile önemli iyileştirmeler yapabilirsiniz...',
                'image' => 'https://cdn.yemek.com/mnresize/1250/833/uploads/2022/03/tart-kalibinda-elmali-turta-hr.jpg',
                'category_id' => Category::where('name', 'Sağlık')->first()->id,
                'user_id' => $adminUser->id,
                'tags' => ['Sağlık,Yaşam Tarzı'],
                'status' => true,
                'start_date' => now(),
                'end_date' => null
            ],
            [
                'title' => '2024 İçin En İyi 5 Seyahat Noktası',
                'content' => 'Bir sonraki tatilinizi planlıyorsanız, 2024 için bu en iyi beş destinasyonu göz önünde bulundurun...',
                'image' => 'https://cdn.yemek.com/mnresize/1250/833/uploads/2022/03/tart-kalibinda-elmali-turta-hr.jpg',
                'category_id' => Category::where('name', 'Seyahat')->first()->id,
                'user_id' => $adminUser->id,
                'tags' => ['Seyahat,Destinasyonlar'],
                'status' => true,
                'start_date' => now(),
                'end_date' => null
            ],
            [
                'title' => 'Spor Beslenmesi İçin Nihai Rehber',
                'content' => 'Spor beslenmesi, her seviyedeki sporcu için önemlidir. Bu rehber, bilmeniz gereken temel bilgileri kapsıyor...',
                'image' => 'https://cdn.yemek.com/mnresize/1250/833/uploads/2022/03/tart-kalibinda-elmali-turta-hr.jpg',
                'category_id' => Category::where('name', 'Spor')->first()->id,
                'user_id' => $adminUser->id,
                'tags' => ['Spor,Beslenme'],
                'status' => true,
                'start_date' => now(),
                'end_date' => null
            ],
            [
                'title' => 'Gıda Endüstrisindeki Yenilikler',
                'content' => 'Gıda endüstrisi, bitki bazlı etten laboratuvarda yetiştirilen yiyeceklere kadar bir dizi yenilikle karşı karşıya...',
                'image' => 'https://cdn.yemek.com/mnresize/1250/833/uploads/2022/03/tart-kalibinda-elmali-turta-hr.jpg',
                'category_id' => Category::where('name', 'Yiyecek')->first()->id,
                'user_id' => $adminUser->id,
                'tags' => ['Gıda,Yenilik'],
                'status' => true,
                'start_date' => now(),
                'end_date' => null
            ],
            [
                'title' => 'İş ve Özel Hayat Dengesi Kurmanın Yolları',
                'content' => 'İş ve özel hayat arasında denge kurmak zor olabilir. Bu dengeyi sağlamak için bazı ipuçları...',
                'image' => 'https://cdn.yemek.com/mnresize/1250/833/uploads/2022/03/tart-kalibinda-elmali-turta-hr.jpg',
                'category_id' => Category::where('name', 'Yaşam Tarzı')->first()->id,
                'user_id' => $adminUser->id,
                'tags' => ['Yaşam Tarzı,Denge'],
                'status' => true,
                'start_date' => now(),
                'end_date' => null
            ],
            [
                'title' => 'Başarılı Bir İş Kurmanın Yolları',
                'content' => 'İş kurmak göz korkutucu bir görev olabilir. Bu makale, başlamanıza yardımcı olacak adım adım bir rehber sunuyor...',
                'image' => 'https://cdn.yemek.com/mnresize/1250/833/uploads/2022/03/tart-kalibinda-elmali-turta-hr.jpg',
                'category_id' => Category::where('name', 'İş')->first()->id,
                'user_id' => $adminUser->id,
                'tags' => ['İş,Startup'],
                'status' => true,
                'start_date' => now(),
                'end_date' => null
            ],
            [
                'title' => 'Eğlencenin Toplum Üzerindeki Etkisi',
                'content' => 'Eğlence, toplumu şekillendirmede önemli bir rol oynar. Bu makale, çeşitli eğlence türlerinin etkisini inceliyor...',
                'image' => 'https://cdn.yemek.com/mnresize/1250/833/uploads/2022/03/tart-kalibinda-elmali-turta-hr.jpg',
                'category_id' => Category::where('name', 'Eğlence')->first()->id,
                'user_id' => $adminUser->id,
                'tags' => ['Eğlence,Toplum'],
                'status' => true,
                'start_date' => now(),
                'end_date' => null
            ],
            [
                'title' => 'Bilimdeki Son Atılımlar',
                'content' => 'Bilim sürekli evriliyor. İşte dünyayı değiştiren en son atılımlar...',
                'image' => 'https://cdn.yemek.com/mnresize/1250/833/uploads/2022/03/tart-kalibinda-elmali-turta-hr.jpg',
                'category_id' => Category::where('name', 'Bilim')->first()->id,
                'user_id' => $adminUser->id,
                'tags' => ['Bilim,Atılımlar'],
                'status' => true,
                'start_date' => now(),
                'end_date' => null
            ],
        ];

        foreach ($posts as $postData) {
            $tags = $postData['tags'];
            unset($postData['tags']);
            $post = Post::create($postData);

            $tagIds = [];
            foreach ($tags as $tagName) {
                $category_id = $postData['category_id'];
                $tag = Tag::firstOrCreate(['name' => $tagName, 'category_id' => $category_id]);
                $tagIds[] = $tag->id;
            }

            $post->tags()->sync($tagIds);
        }
    }
}