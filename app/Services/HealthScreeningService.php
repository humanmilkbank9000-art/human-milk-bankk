<?php

namespace App\Services;

use App\Models\HealthScreening;
use App\Models\Admin;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class HealthScreeningService
{
    public function create(array $data, int $userId)
    {
        if (HealthScreening::where('user_id', $userId)->exists()) {
            throw new \RuntimeException('You have already submitted your health screening.');
        }

        $payload = array_merge($data, [
            'user_id' => $userId,
            'status' => 'pending',
            'date_accepted' => null,
            'date_declined' => null,
        ]);

        $screening = HealthScreening::create($payload);

        // Notify admins
        $this->notifyAdmins('New Health Screening', 'A user submitted a health screening for review.');

        return $screening;
    }

    public function accept(HealthScreening $screening, ?string $comments = null)
    {
        $screening->status = 'accepted';
        $screening->date_accepted = now();
        if (!empty($comments)) {
            $screening->admin_notes = $comments;
        }
        $screening->save();

        $user = $screening->user;
        if ($user) {
            $user->notify(new \App\Notifications\SystemAlert('Health Screening Accepted', 'Your health screening has been accepted. You may now donate.'));
        }

        return $screening;
    }

    public function reject(HealthScreening $screening, ?string $comments = null)
    {
        $screening->status = 'declined';
        $screening->date_declined = now();
        if (!empty($comments)) {
            $screening->admin_notes = $comments;
        }
        $screening->save();

        $user = $screening->user;
        if ($user) {
            $user->notify(new \App\Notifications\SystemAlert('Health Screening Declined', 'Your health screening has been declined.'));
        }

        return $screening;
    }

    public function undoDecline(HealthScreening $screening, ?string $comments = null)
    {
        if ($screening->status !== 'declined') {
            throw new \RuntimeException('Only declined screenings can be undone.');
        }

        $screening->status = 'accepted';
        $screening->date_accepted = now();
        $screening->date_declined = null;
        if (!empty($comments)) {
            $screening->admin_notes = $comments;
        }
        $screening->save();

        $user = $screening->user;
        if ($user) {
            $user->notify(new \App\Notifications\SystemAlert('Health Screening Accepted', 'Your previously declined health screening has been reviewed and accepted. You may now donate.'));
        }

        return $screening;
    }

    protected function notifyAdmins(string $title, string $message)
    {
        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\SystemAlert($title, $message));
        }
    }

    public function getQuestionSections(): array
    {
        return [
            'medical_history' => [
                ['Have you donated breastmilk before?', 'Nakahatag/naka-donar ka na ba sa imung gatas kaniadto?', false],
                ['Have you for any reason been deferred as a breastmilk donor? If yes, specify reason', 'Naballbaran na ba ka nga mag-donar sa imung gatas kaniadto? Kung oo, unsay hinungdan?', true],
                ['Did you have a normal pregnancy and delivery for your most recent pregnancy?', 'Wala ka bay naaging kalisod og komplikasyon sa pinakaulahi nimung pagburos og pagpanganak?', false],
                ['Do you have any acute or chronic infection such as tuberculosis, hepatitis, systemic disorders? If yes, specify', 'Aduna ka bay gibating mga sakit sama sa Tuberculosis, sakit sa atay or sakit sa dugo? Kung naa, unsa man kini?', true],
                ['Have you been diagnosed with a chronic non-infectious illness such as diabetes, hypertension, heart disease? If yes, specify', 'Nadayagnos ka ba nga adunay lagay nga dili makatakod nga sakit sama sa diabetes, altapresyon, sakit sa kasingkasing? Kung naa, unsa man kini?', true],
                ['Have you received any blood transfusion or blood products within the last 12 months?', 'Naabunohan ka ba ug dugo sulod sa niaging 12 ka buwan?', false],
                ['Have you received any organ or tissue transplant within the last 12 months?', 'Niagi ka ba ug operasyon din nidawat ka ug bahin/parte sa lawas sulod sa nlilabay nga 12 ka bulan?', false],
                ['Have you had any intake of alcohol within the last 24 hours? If yes, how much', 'Sulod sa 24 oras, naka inum ka ba og bisan unsang ilimnong makahubog? Kung oo, unsa ka daghan?', true],
                ['Do you use megadose vitamins or pharmacologically active herbal preparations?', 'Gainum ka ba og sobra sa gitakda na mga bitamina og mga produktong adunay sagol na herbal?', false],
                ['Do you regularly use medications such as hormones, antidiabetics, blood thinners? If yes, specify', 'Kanunay ba ka gagamit o gainum sa mga tambal kung lain ang paminaw sa lawas? Og gainum ka ba sa mga tambal pampugong sa pagburos? Kung oo, unsa ngalan sa tambal?', true],
                ['Are you a total vegetarian/vegan? If yes, do you supplement with vitamins', 'Ikaw ba dili gakaon sa lain pagkaon kundi utan lang? Kung oo, gainum ka ba mga bitamina?', true],
                ['Do you use illicit drugs?', 'Gagamit ka ba sa ginadilina mga droga?', false],
                ['Do you smoke? If yes, how many per day', 'Gapanigarilyo ka ba? Kung oo, pila ka "stick" o pack se ise ka adlaw?', true],
                ['Are you around people who smoke (passive smoking)?', 'Doul ba ka permi sa mga tao nga gapanigarilyo?', false],
                ['Have you had breast augmentation surgery using silicone implants?', 'Kaw ba niagi ug operasyon sa imung suso din nagpabutang ug "silicone" O artipisyal na suso?', false],
            ],
            'sexual_history' => [
                ['Have you ever had Syphilis, HIV, herpes or any STD?', 'Niagi ka ba og bisan unsang sakit sa kinatawo? Sakit na makuha pinaagi sa pakighilawas?', false],
                ['Do you have multiple sexual partners?', 'Aduna ka bay lain pares sa pakighilawas gawas sa imu bana/kapikas?', false],
                ['Have you had a sexual partner with risk factors (bisexual, promiscuous, STD, blood transfusion, IV drug use)? If yes, specify', 'Niagi ka ba og pakighilawas ning mga mosunod? Kung oo, specify', true],
                ['Have you had a tattoo, accidental needlestick or contact with someone else\'s blood? If yes, specify', 'Niagi ka ba og papatik sukad? Niagi ka ba og katusok sa bisan unsang dagom? Kung oo, specify', true],
            ],
            'donor_infant' => [
                ['Is your child healthy?', 'Himsog ba ang imung anak?', false],
                ['Was your child delivered full term?', 'Gipanganak ba siya sa saktong buwan?', false],
                ['Are you exclusively breastfeeding your child?', 'Kaugalingong gatas lang ba nimu ang gipalnum sa bata?', false],
                ['Is/was your youngest child jaundiced? If yes, specify age and duration', 'Imung kinamanghuran na bata ba niagi og pagdalag sa pamanit? Kung oo, pilay edad sa bata ato nga higayon? Ug unsa ang kadugayon sa pagdalag?', true],
                ['Have you ever received breastmilk from another mother? If yes, specify when', 'Nakadawat ba ang imung anak og gatas sa laing inahan? Kung oo, kanus.a kini nahtabo?', true],
            ]
        ];
    }
}
