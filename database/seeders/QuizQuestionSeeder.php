<?php

namespace Database\Seeders;

use App\Models\Coin;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;

class QuizQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $denarius = Coin::where('slug', 'denarius-trajan-roma')->first() ?? Coin::first();
        $coinId = $denarius ? $denarius->id : 1;

        $questions = [
            [
                'coin_id' => $coinId,
                'question_type' => 'which_emperor',
                'question_text' => 'Görseldeki "IMP CAES NERVA TRAIAN AVG GERM" lejantına ve defne taçlı büst tasvirine sahip sikke hangi Roma imparatoruna aittir?',
                'options_json' => [
                    'Nerva',
                    'Traianus (Trajan)',
                    'Hadrianus',
                    'Antoninus Pius',
                ],
                'correct_option_index' => 1,
                'explanation' => 'Lejantta yer alan "NERVA TRAIAN" ibaresi ve optimus princeps unvanı İmparator Trajan\'a (M.S. 98-117) işaret eder.',
            ],
            [
                'coin_id' => $coinId,
                'question_type' => 'guess_metal',
                'question_text' => 'Roma İmparatorluğu\'nda standart gümüş sikke birimi olarak yüzyıllarca kullanılan, yaklaşık 3.4 gram ağırlığındaki nominal hangisidir?',
                'options_json' => [
                    'Aureus',
                    'Sestertius',
                    'Denarius',
                    'As',
                ],
                'correct_option_index' => 2,
                'explanation' => 'Denarius, M.Ö. 211\'den M.S. 3. yüzyılın ortalarına kadar Roma\'nın ana gümüş sikkesidir. Aureus altın, Sestertius ise orikalkum/tunçtur.',
            ],
            [
                'coin_id' => $coinId,
                'question_type' => 'correct_translation',
                'question_text' => 'Roma sikkelerinde sıkça karşılaşılan "S C" (Senatus Consulto) kısaltmasının Türkçe akademik anlamı nedir?',
                'options_json' => [
                    'Senato Kararıyla / İzniyle Basılmıştır',
                    'Kutsal Sezar',
                    'Sürekli Konsül',
                    'Güneş ve Ay Tasviri',
                ],
                'correct_option_index' => 0,
                'explanation' => '"Senatus Consulto" (Senato Kararıyla), özellikle bronz ve pirinç sikkelerde senatonun basım onayını gösteren resmi nümizmatik damgadır.',
            ],
            [
                'coin_id' => $coinId,
                'question_type' => 'real_or_fake',
                'question_text' => 'Antik bir sikkede pürüzsüz kenar dikişi (seam line) ve mikroskobik gaz baloncukları (casting bubbles) gözleniyorsa bu neyi gösterir?',
                'options_json' => [
                    'Yüksek gümüş ayarını',
                    'Döküm (Cast) sahtesi olduğunu',
                    'Orijinal darp kalıbının aşındığını',
                    'Kondisyonun Çok Temiz (XF) olduğunu',
                ],
                'correct_option_index' => 1,
                'explanation' => 'Antik sikkeler çekiçle darp (struck) edilir. Dikiş izi ve gaz gözenekleri modern kalıp döküm (cast counterfeit) sahteciliğinin belirgin kanıtıdır.',
            ],
        ];

        foreach ($questions as $q) {
            QuizQuestion::updateOrCreate(
                [
                    'coin_id' => $q['coin_id'],
                    'question_type' => $q['question_type'],
                    'question_text' => $q['question_text'],
                ],
                $q
            );
        }
    }
}

