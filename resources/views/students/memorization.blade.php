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
    .progress-bar-container {
        background-color: #e0e0e0;
        border-radius: 15px;
        overflow: hidden;
        height: 30px;
        border: 3px solid #2a2a2a;
        margin-bottom: 30px;
    }
    .progress-bar {
        background: linear-gradient(135deg, #0a5c36, #1abc9c);
        height: 100%;
        width: 25%; /* Example width */
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        transition: width 0.5s ease-in-out;
    }
    .section-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        border: 3px solid #2a2a2a;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }
    .section-title {
        font-size: 1.6rem;
        color: #000 !important;
        font-weight: 800;
        margin-bottom: 20px;
    }
</style>

<div class="section-card mb-4">
    <h2 class="section-title">Overall Memorization Progress</h2>
    <div class="progress-bar-container">
        <div class="progress-bar" style="width: 10%;">10%</div>
    </div>
</div>


<div class="section-card">
    <h2 class="section-title">Surah Status</h2>
    <p class="text-gray-500 mb-4">Click on a Surah to view and update your memorization progress for each Ayah.</p>
    <div class="memorization-grid">
        @php
            $surahs = [
                1 => 'Al-Fatiha', 78 => 'An-Naba', 79 => 'An-Nazi\'at', 80 => 'Abasa', 81 => 'At-Takwir', 82 => 'Al-Infitar',
                83 => 'Al-Mutaffifin', 84 => 'Al-Inshiqaq', 85 => 'Al-Buruj', 86 => 'At-Tariq', 87 => 'Al-A\'la',
                88 => 'Al-Ghashiyah', 89 => 'Al-Fajr', 90 => 'Al-Balad', 91 => 'Ash-Shams', 92 => 'Al-Lail',
                93 => 'Ad-Duhaa', 94 => 'Ash-Sharh', 95 => 'At-Tin', 96 => 'Al-Alaq', 97 => 'Al-Qadr', 98 => 'Al-Bayyinah',
                99 => 'Az-Zalzalah', 100 => 'Al-Adiyat', 101 => 'Al-Qari\'ah', 102 => 'At-Takathur', 103 => 'Al-Asr',
                104 => 'Al-Humazah', 105 => 'Al-Fil', 106 => 'Quraysh', 107 => 'Al-Ma\'un', 108 => 'Al-Kawthar',
                109 => 'Al-Kafirun', 110 => 'An-Nasr', 111 => 'Al-Masad', 112 => 'Al-Ikhlas', 113 => 'Al-Falaq', 114 => 'An-Nas'
            ];
        @endphp

        @foreach ($surahs as $number => $name)
            <div class="surah-card">
                <div class="surah-number">{{ $number }}</div>
                <div class="surah-name">{{ $name }}</div>
            </div>
        @endforeach
    </div>
</div>
@endsection
