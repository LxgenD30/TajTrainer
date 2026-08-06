@extends('layouts.dashboard')

@section('title', 'Memorization Tracker')
@section('user-role', 'Student • Learning Portal')

@section('navigation')
    @include('partials.student-nav')
@endsection

@section('content')
<style>
    .memorization-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }
    .surah-card {
        background: #fff;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        border: 3px solid #2a2a2a;
        box-shadow: 0 8px 15px rgba(0,0,0,0.07);
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .surah-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px rgba(0,0,0,0.1);
    }
    .surah-card .surah-number {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1abc9c;
    }
    .surah-card .surah-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #333;
        margin-top: 5px;
    }
    .section-card {
        background: white;
        border-radius: 18px;
        padding: 28px;
        border: 3px solid #2a2a2a;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }

    /* Card Heading */
    .mem-card-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
        padding-bottom: 18px;
        border-bottom: 3px solid #f5f5dc;
        flex-wrap: wrap;
    }
    .mem-header-icon {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        background: linear-gradient(135deg, #0a5c36, #2e8b57);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 8px 18px rgba(10, 92, 54, 0.28);
        flex-shrink: 0;
    }
    .mem-header-text {
        flex: 1;
        min-width: 200px;
    }
    .mem-header-title {
        font-size: 1.6rem;
        color: #0a5c36 !important;
        font-weight: 800;
        margin: 0;
        font-family: 'El Messiri', serif;
        line-height: 1.2;
    }
    .mem-header-sub {
        color: #333;
        font-size: 1.05rem;
        margin: 5px 0 0;
        font-weight: 600;
    }
    .mem-header-count {
        background: linear-gradient(135deg, #0a5c36, #2e8b57);
        color: #fff;
        padding: 9px 20px;
        border-radius: 50px;
        font-weight: 800;
        font-size: 0.98rem;
        box-shadow: 0 6px 14px rgba(10, 92, 54, 0.22);
        white-space: nowrap;
    }

    /* Welcome Banner (consistent student theme) */
    .welcome-banner {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        border-radius: 25px;
        padding: 40px;
        margin-bottom: 30px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(10, 92, 54, 0.25);
        border: 3px solid #2a2a2a;
    }
    .welcome-banner:before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
        opacity: 0.4;
    }
    .welcome-content { position: relative; z-index: 2; }
    .welcome-content h1 { font-size: 2.5rem; margin-bottom: 10px; font-weight: 700; color: #ffffff; text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3); display: flex; align-items: center; gap: 12px; }
    .welcome-content p { font-size: 1.25rem; opacity: 0.95; line-height: 1.6; }
</style>

<section class="welcome-banner">
    <div class="welcome-content">
        <h1><i class="fas fa-brain"></i> Memorization Tracker</h1>
        <p>Track your Qur'an memorization journey across all 114 surahs</p>
    </div>
</section>

<div class="section-card">
    <div class="mem-card-header">
        <div class="mem-header-icon">
            <i class="fas fa-brain"></i>
        </div>
        <div class="mem-header-text">
            <h2 class="mem-header-title">Surah Status</h2>
            <p class="mem-header-sub">Click on a Surah to view ayahs and start memorizing</p>
        </div>
        <span class="mem-header-count">114 Surahs</span>
    </div>
    <div class="memorization-grid">
        @php
            $surahs = [
                1=>'Al-Fatiha',2=>'Al-Baqarah',3=>"Ali 'Imran",4=>"An-Nisa",5=>"Al-Ma'idah",
                6=>"Al-An'am",7=>"Al-A'raf",8=>'Al-Anfal',9=>'At-Tawbah',10=>'Yunus',
                11=>'Hud',12=>'Yusuf',13=>"Ar-Ra'd",14=>'Ibrahim',15=>'Al-Hijr',
                16=>'An-Nahl',17=>"Al-Isra",18=>'Al-Kahf',19=>'Maryam',20=>'Ta-Ha',
                21=>"Al-Anbiya",22=>'Al-Hajj',23=>"Al-Mu'minun",24=>'An-Nur',25=>'Al-Furqan',
                26=>"Ash-Shu'ara",27=>'An-Naml',28=>'Al-Qasas',29=>"Al-'Ankabut",30=>'Ar-Rum',
                31=>'Luqman',32=>'As-Sajdah',33=>'Al-Ahzab',34=>'Saba',35=>'Fatir',
                36=>'Ya-Sin',37=>"As-Saffat",38=>'Sad',39=>'Az-Zumar',40=>'Ghafir',
                41=>'Fussilat',42=>"Ash-Shura",43=>'Az-Zukhruf',44=>'Ad-Dukhan',45=>'Al-Jathiyah',
                46=>'Al-Ahqaf',47=>'Muhammad',48=>'Al-Fath',49=>'Al-Hujurat',50=>'Qaf',
                51=>'Adh-Dhariyat',52=>'At-Tur',53=>'An-Najm',54=>'Al-Qamar',55=>'Ar-Rahman',
                56=>"Al-Waqi'ah",57=>'Al-Hadid',58=>'Al-Mujadila',59=>'Al-Hashr',60=>'Al-Mumtahanah',
                61=>'As-Saf',62=>"Al-Jumu'ah",63=>'Al-Munafiqun',64=>'At-Taghabun',65=>'At-Talaq',
                66=>'At-Tahrim',67=>'Al-Mulk',68=>'Al-Qalam',69=>'Al-Haqqah',70=>"Al-Ma'arij",
                71=>'Nuh',72=>'Al-Jinn',73=>'Al-Muzzammil',74=>'Al-Muddaththir',75=>'Al-Qiyamah',
                76=>'Al-Insan',77=>'Al-Mursalat',78=>"An-Naba",79=>"An-Nazi'at",80=>"'Abasa",
                81=>'At-Takwir',82=>'Al-Infitar',83=>'Al-Mutaffifin',84=>'Al-Inshiqaq',85=>'Al-Buruj',
                86=>'At-Tariq',87=>"Al-A'la",88=>'Al-Ghashiyah',89=>'Al-Fajr',90=>'Al-Balad',
                91=>'Ash-Shams',92=>'Al-Layl',93=>'Ad-Duha',94=>'Ash-Sharh',95=>'At-Tin',
                96=>"Al-'Alaq",97=>'Al-Qadr',98=>'Al-Bayyinah',99=>'Az-Zalzalah',100=>"Al-'Adiyat",
                101=>"Al-Qari'ah",102=>'At-Takathur',103=>"Al-'Asr",104=>'Al-Humazah',105=>'Al-Fil',
                106=>'Quraysh',107=>"Al-Ma'un",108=>'Al-Kawthar',109=>'Al-Kafirun',110=>'An-Nasr',
                111=>'Al-Masad',112=>'Al-Ikhlas',113=>'Al-Falaq',114=>'An-Nas',
            ];
        @endphp

        @foreach ($surahs as $number => $name)
            <a href="{{ route('student.memorization.surah', ['surah_number' => $number]) }}" class="surah-card" style="text-decoration: none;">
                <div class="surah-number">{{ $number }}</div>
                <div class="surah-name">{{ $name }}</div>
            </a>
        @endforeach
    </div>
</div>
@endsection
