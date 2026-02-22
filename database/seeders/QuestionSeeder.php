<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Seed 30 validated Big Five questions.
     * Based on the IPIP-NEO domain items, adapted for the platform.
     *
     * Scoring:
     *  - Likert 1–5
     *  - is_reverse = true → score = 6 - likert_score
     *  - 6 questions per trait
     */
    public function run(): void
    {
        $questions = [
            // ── OPENNESS ──────────────────────────────────────────────
            ['trait' => 'openness', 'question_text' => 'Saya memiliki imajinasi yang hidup.', 'is_reverse' => false, 'allow_note' => false, 'order_number' => 1,
                'options' => ['Sangat realistis dan praktis', 'Lebih suka hal yang nyata', 'Kadang berimajinasi', 'Sering memikirkan ide-ide kreatif', 'Sangat imajinatif dan penuh ide baru']],
            ['trait' => 'openness', 'question_text' => 'Saya menikmati berpikir tentang konsep abstrak.', 'is_reverse' => false, 'allow_note' => true,  'order_number' => 2,
                'options' => ['Sangat tidak suka teori', 'Lebih suka praktik langsung', 'Tergantung topik bahasan', 'Cukup menikmati diskusi konseptual', 'Sangat menyukai pemikiran mendalam']],
            ['trait' => 'openness', 'question_text' => 'Saya mencari pengalaman dan petualangan baru.', 'is_reverse' => false, 'allow_note' => false, 'order_number' => 3,
                'options' => ['Menghindari hal asing sama sekali', 'Lebih suka rutinitas', 'Kadang mencoba hal baru', 'Senang menjelajah', 'Selalu antusias mencari pengalaman baru']],
            ['trait' => 'openness', 'question_text' => 'Saya lebih suka berpegang pada hal yang saya kenal.', 'is_reverse' => true,  'allow_note' => false, 'order_number' => 4,
                'options' => ['Sangat suka mencoba yang belum pasti', 'Sering mencari variasi', 'Bisa fleksibel', 'Cenderung nyaman dengan rutinitas', 'Sangat kaku dan menolak hal baru']],
            ['trait' => 'openness', 'question_text' => 'Saya menghargai seni dan keindahan dalam berbagai bentuk.', 'is_reverse' => false, 'allow_note' => true,  'order_number' => 5,
                'options' => ['Sama sekali tidak tertarik pada seni', 'Jarang mengapresiasi', 'Menikmati seni pop biasa', 'Cukup menghargai karya seni', 'Sangat terhubung emosional dengan karya seni']],
            ['trait' => 'openness', 'question_text' => 'Saya merasa diskusi filosofis membosankan.', 'is_reverse' => true,  'allow_note' => false, 'order_number' => 6,
                'options' => ['Sangat menikmati diskusi makna hidup', 'Sering memikirkannya', 'Bisa terlibat jika perlu', 'Sering merasa itu buang waktu', 'Sangat membosankan dan tak berguna']],

            // ── CONSCIENTIOUSNESS ─────────────────────────────────────
            ['trait' => 'conscientiousness', 'question_text' => 'Saya menyelesaikan tugas tepat waktu.', 'is_reverse' => false, 'allow_note' => false, 'order_number' => 7,
                'options' => ['Hampir selalu terlambat', 'Sering menunda', 'Kadang telat sedikit', 'Biasanya tepat waktu', 'Selalu selesai jauh sebelum tenggat waktu']],
            ['trait' => 'conscientiousness', 'question_text' => 'Saya mengikuti jadwal dan mematuhinya.', 'is_reverse' => false, 'allow_note' => false, 'order_number' => 8,
                'options' => ['Sangat spontan tanpa rencana', 'Sering mengabaikan jadwal', 'Kadang memakai jadwal', 'Berusaha disiplin pada agenda', 'Sangat ketat mematuhi jadwal harian']],
            ['trait' => 'conscientiousness', 'question_text' => 'Saya memperhatikan detail dalam setiap hal yang saya lakukan.', 'is_reverse' => false, 'allow_note' => true,  'order_number' => 9,
                'options' => ['Sangat ceroboh', 'Cenderung melihat gambaran besar saja', 'Cukup detail pada hal penting', 'Sangat teliti', 'Perfeksionis pada setiap detail kecil']],
            ['trait' => 'conscientiousness', 'question_text' => 'Saya sering membiarkan tugas tidak selesai.', 'is_reverse' => true,  'allow_note' => false, 'order_number' => 10,
                'options' => ['Selalu menuntaskan apapun yang dimulai', 'Jarang meninggalkan tanggung jawab', 'Kadang lupa menyelesaikannya', 'Sering lompat ke tugas lain', 'Hampir tak pernah menyelesaikan apapun']],
            ['trait' => 'conscientiousness', 'question_text' => 'Saya bekerja keras untuk mencapai tujuan saya.', 'is_reverse' => false, 'allow_note' => false, 'order_number' => 11,
                'options' => ['Sangat mudah menyerah', 'Kurang punya tekad', 'Cukup termotivasi', 'Sangat tekun dan ambisius', 'Bekerja tanpa henti demi mencapainya']],
            ['trait' => 'conscientiousness', 'question_text' => 'Saya sering tidak teratur.', 'is_reverse' => true,  'allow_note' => false, 'order_number' => 12,
                'options' => ['Sangat rapi dan sistematis', 'Biasanya menjaga keteraturan', 'Kadang meletakkan barang sembarangan', 'Sering berantakan', 'Sangat kacau dan tidak rapi sama sekali']],

            // ── EXTRAVERSION ──────────────────────────────────────────
            ['trait' => 'extraversion', 'question_text' => 'Saya merasa nyaman berada di sekitar orang-orang.', 'is_reverse' => false, 'allow_note' => false, 'order_number' => 13,
                'options' => ['Sangat panik dan ingin kabur', 'Merasa canggung', 'Nyaman pada kelompok kecil', 'Cukup santai', 'Sangat menikmati dan merasa hidup']],
            ['trait' => 'extraversion', 'question_text' => 'Saya menikmati menjadi pusat perhatian.', 'is_reverse' => false, 'allow_note' => true,  'order_number' => 14,
                'options' => ['Sangat benci diperhatikan', 'Berusaha menghindar', 'Biasa saja', 'Menikmatinya sesekali', 'Selalu berusaha menjadi bintang panggung']],
            ['trait' => 'extraversion', 'question_text' => 'Saya mudah memulai percakapan dengan orang tak dikenal.', 'is_reverse' => false, 'allow_note' => false, 'order_number' => 15,
                'options' => ['Tidak pernah melakukannya', 'Menunggu disapa duluan', 'Tergantung situasinya', 'Cukup mudah menyapa', 'Selalu semangat mengajak ngobrol siapapun']],
            ['trait' => 'extraversion', 'question_text' => 'Saya lebih suka menghabiskan malam sendirian.', 'is_reverse' => true,  'allow_note' => false, 'order_number' => 16,
                'options' => ['Selalu butuh keramaian dan hangout', 'Sering keluar malam', 'Keduanya sama menyenangkan', 'Lebih memilih santai di rumah', 'Tidak suka keluar rumah di malam hari']],
            ['trait' => 'extraversion', 'question_text' => 'Saya merasa bersemangat setelah menghabiskan waktu bersama orang lain.', 'is_reverse' => false, 'allow_note' => true,  'order_number' => 17,
                'options' => ['Merasa sangat kehabisan energi (drained)', 'Cepat lelah', 'Tergantung lawan bicara', 'Merasa enerjik kembali', 'Sangat terpacu dan hiperaktif']],
            ['trait' => 'extraversion', 'question_text' => 'Saya merasa acara sosial itu melelahkan.', 'is_reverse' => true,  'allow_note' => false, 'order_number' => 18,
                'options' => ['Acara sosial membuat saya sangat berenergi', 'Sangat menyenangkan', 'Tergantung siapa saja yang datang', 'Cukup menguras tenaga', 'Sangat melelahkan secara fisik dan mental']],

            // ── AGREEABLENESS ─────────────────────────────────────────
            ['trait' => 'agreeableness', 'question_text' => 'Saya peduli dengan perasaan orang lain.', 'is_reverse' => false, 'allow_note' => false, 'order_number' => 19,
                'options' => ['Peduli pada urusanku sendiri saja', 'Kurang begitu memikirkannya', 'Saya pikirkan sewajarnya', 'Cukup peka', 'Sangat sensitif terhadap perasaan orang lain']],
            ['trait' => 'agreeableness', 'question_text' => 'Saya selalu bersedia membantu mereka yang membutuhkan.', 'is_reverse' => false, 'allow_note' => true,  'order_number' => 20,
                'options' => ['Menolak membantu sama sekali', 'Hanya membantu jika dipaksa', 'Membantu jika sedang luang', 'Sering menawarkan bantuan', 'Rela mengorbankan diri sendiri demi membantu']],
            ['trait' => 'agreeableness', 'question_text' => 'Saya berusaha menghindari konflik dalam hubungan.', 'is_reverse' => false, 'allow_note' => false, 'order_number' => 21,
                'options' => ['Sangat suka menantang dan berdebat', 'Sering berselisih', 'Bisa berargumen jika benar', 'Suka mencari jalan tengah', 'Selalu mengalah demi perdamaian']],
            ['trait' => 'agreeableness', 'question_text' => 'Saya sering berdebat dengan orang lain.', 'is_reverse' => true,  'allow_note' => false, 'order_number' => 22,
                'options' => ['Selalu sepakat dengan siapapun', 'Sangat jarang membantah', 'Kadang berbeda pendapat', 'Sering berargumen panas', 'Setiap hari selalu mencari keributan']],
            ['trait' => 'agreeableness', 'question_text' => 'Saya percaya pada niat baik orang lain.', 'is_reverse' => false, 'allow_note' => false, 'order_number' => 23,
                'options' => ['Sangat curiga bahwa semua orang licik', 'Sulit percaya', 'Tergantung seberapa kenal', 'Cenderung mudah percaya', 'Sangat naif dan memercayai siapapun']],
            ['trait' => 'agreeableness', 'question_text' => 'Saya memprioritaskan kebutuhan saya sendiri di atas orang lain.', 'is_reverse' => true,  'allow_note' => false, 'order_number' => 24,
                'options' => ['Selalu mengutamakan orang banyak', 'Sering mengalah', 'Berbagi secara adil', 'Lebih condong pada kebutuhanku', 'Sangat egois dan hanya peduli diri sendiri']],

            // ── NEUROTICISM ───────────────────────────────────────────
            ['trait' => 'neuroticism', 'question_text' => 'Saya sering khawatir tentang banyak hal.', 'is_reverse' => false, 'allow_note' => true,  'order_number' => 25,
                'options' => ['Sangat santai tanpa pikiran beban', 'Jarang cemas', 'Khawatir pada hal yang masuk akal', 'Sering merasa waswas', 'Sangat stres memikirkan segalanya hingga sulit tidur']],
            ['trait' => 'neuroticism', 'question_text' => 'Saya mudah stres.', 'is_reverse' => false, 'allow_note' => false, 'order_number' => 26,
                'options' => ['Tahan banting dalam kondisi apapun', 'Tetap tenang', 'Bisa stres jika terlalu menumpuk', 'Sering merasa kewalahan', 'Sangat rapuh dan gampang hancur']],
            ['trait' => 'neuroticism', 'question_text' => 'Suasana hati saya banyak berubah sepanjang hari.', 'is_reverse' => false, 'allow_note' => false, 'order_number' => 27,
                'options' => ['Emosi sangat stabil bagai batu', 'Jarang berubah mendadak', 'Perubahan kecil sewajarnya', 'Lumayan tidak menentu (moody)', 'Bisa menangis dan tertawa di saat bersamaan']],
            ['trait' => 'neuroticism', 'question_text' => 'Saya tetap tenang dalam situasi tertekan.', 'is_reverse' => true,  'allow_note' => false, 'order_number' => 28,
                'options' => ['Langsung panik dan histeris', 'Gemetar dan tidak fokus', 'Berusaha tegar', 'Cukup tenang mengatur strategi', 'Sangat dingin tak tergoyahkan (ice cold)']],
            ['trait' => 'neuroticism', 'question_text' => 'Saya sering merasa cemas tanpa alasan yang jelas.', 'is_reverse' => false, 'allow_note' => true,  'order_number' => 29,
                'options' => ['Tidak pernah merasakannya', 'Jarang terjadi', 'Hanya cemas karena sebab pasti', 'Sering merasa gelisah', 'Selalu dihantui kecemasan berlebihan (anxiety)']],
            ['trait' => 'neuroticism', 'question_text' => 'Saya menangani situasi sulit tanpa banyak stres.', 'is_reverse' => true,  'allow_note' => false, 'order_number' => 30,
                'options' => ['Sangat hancur dan menyerah', 'Sering pusing memikirkannya', 'Menangkalinya perlahan', 'Mampu bersikap rasional', 'Bisa menyelesaikannya sambil tetap rileks']],
        ];

        foreach ($questions as $q) {
            Question::updateOrCreate(
                ['order_number' => $q['order_number']],
                array_merge($q, ['active' => true])
            );
        }

        $this->command->info('30 Big Five questions seeded successfully.');
    }
}
