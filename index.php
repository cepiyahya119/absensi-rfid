<?php
include "config/koneksi.php";

date_default_timezone_set("Asia/Jakarta");

// Variabel hasil scan
 $message_type = "";
 $msg_nama = "";
 $msg_kelas = "";
 $msg_jam = "";
 $msg_status = "";

 $batas_datang = "08:00:00";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['uid'])) {
        $uid = mysqli_real_escape_string($conn, trim($_POST['uid']));
        if (!empty($uid)) {
            $querySiswa = mysqli_query($conn, "SELECT * FROM siswa WHERE rfid_uid='$uid'");
            if (mysqli_num_rows($querySiswa) > 0) {
                $siswa = mysqli_fetch_assoc($querySiswa);
                $siswa_id = $siswa['id'];
                $nama     = $siswa['nama'];
                $kelas    = $siswa['kelas'];
                $tanggal  = date("Y-m-d");
                $jam      = date("H:i:s");

                $cek = mysqli_query($conn,
                    "SELECT * FROM absensi WHERE siswa_id='$siswa_id' AND tanggal='$tanggal'");

                if (mysqli_num_rows($cek) == 0) {
                    $status = ($jam > $batas_datang) ? "Terlambat" : "Hadir";
                    
                    // FIX: Menambahkan kolom kelas pada query INSERT
                    mysqli_query($conn,
                        "INSERT INTO absensi (siswa_id,nama,kelas,tanggal,jam_masuk,status)
                         VALUES ('$siswa_id','$nama','$kelas','$tanggal','$jam','$status')");
                         
                    $message_type = "masuk";
                    $msg_nama  = $nama;
                    $msg_kelas = $kelas;
                    $msg_jam   = $jam;
                    $msg_status = $status;
                } else {
                    $data = mysqli_fetch_assoc($cek);
                    if ($data['jam_pulang'] == NULL) {
                        mysqli_query($conn,
                            "UPDATE absensi SET jam_pulang='$jam' WHERE id='".$data['id']."'");
                        $message_type = "pulang";
                        $msg_nama  = $nama;
                        $msg_kelas = $kelas;
                        $msg_jam   = $jam;
                    } else {
                        $message_type = "duplikat";
                        $msg_nama = $nama;
                    }
                }
            } else {
                $message_type = "error";
            }
        }
    }
}

// Statistik hari ini
 $today = date("Y-m-d");
 $qTotal = @mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa");
 $totalSiswa = ($qTotal) ? mysqli_fetch_assoc($qTotal)['total'] : 0;

 $qHadir = @mysqli_query($conn, "SELECT COUNT(*) as total FROM absensi WHERE tanggal='$today' AND status='Hadir'");
 $hadirHariIni = ($qHadir) ? mysqli_fetch_assoc($qHadir)['total'] : 0;

 $qTerlambat = @mysqli_query($conn, "SELECT COUNT(*) as total FROM absensi WHERE tanggal='$today' AND status='Terlambat'");
 $terlambatHariIni = ($qTerlambat) ? mysqli_fetch_assoc($qTerlambat)['total'] : 0;

 $belumAbsen = max(0, $totalSiswa - $hadirHariIni - $terlambatHariIni);

// FIX: Log absensi terbaru menggunakan LEFT JOIN ke tabel siswa untuk memastikan kelas selalu terbaca
 $logQuery = @mysqli_query($conn, "
    SELECT absensi.*, siswa.kelas 
    FROM absensi 
    LEFT JOIN siswa ON absensi.siswa_id = siswa.id 
    WHERE absensi.tanggal='$today' 
    ORDER BY absensi.id DESC LIMIT 6
 ");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sistem Absensi RFID - SMK Bhatara</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
:root {
    --navy: #0f1b3d;
    --gold: #c8973c;
    --bg: #f4f6f9;
    --card: #ffffff;
    --fg: #1e293b;
    --muted: #64748b;
    --border: #e2e8f0;
    --success: #059669;
    --warning: #d97706;
    --danger: #dc2626;
    --info: #2563eb;
}

body {
    font-family: 'Poppins', sans-serif;
    background: var(--bg);
    color: var(--fg);
    margin: 0;
}

/* NAVBAR */
.top-nav {
    background: var(--navy);
    padding: 0 24px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}
.nav-brand {
    display: flex;
    align-items: center;
    gap: 16px;
}
.nav-logos {
    display: flex;
    gap: 10px;
    align-items: center;
}
.nav-text {
    border-left: 1px solid rgba(255,255,255,0.2);
    padding-left: 16px;
}
.nav-school-name {
    font-weight: 700;
    font-size: 16px;
    color: #fff;
}
.nav-school-sub {
    font-size: 11px;
    color: rgba(255,255,255,0.5);
    font-weight: 400;
    letter-spacing: 1px;
}
.nav-right {
    display: flex;
    align-items: center;
    gap: 16px;
}
.btn-login {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 18px;
    border-radius: 6px;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.15);
    color: #fff;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: 0.2s;
}
.btn-login:hover {
    background: var(--gold);
    color: var(--navy);
    border-color: var(--gold);
}

/* LAYOUT */
.main-container {
    max-width: 1100px;
    margin: 24px auto;
    padding: 0 16px;
    display: grid;
    grid-template-columns: 1fr;
    gap: 20px;
}
@media(min-width:1024px) {
    .main-container {
        grid-template-columns: 1.2fr 0.8fr;
    }
}

/* CARDS */
.card-box {
    background: var(--card);
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    border: 1px solid var(--border);
}
.card-header-custom {
    padding: 12px 20px;
    border-bottom: 1px solid var(--border);
    font-weight: 600;
    font-size: 13px;
    color: var(--muted);
    display: flex;
    align-items: center;
    gap: 8px;
}
.card-header-custom i { color: var(--gold); }
.card-body-custom { padding: 20px; }

/* CLOCK */
.clock-card {
    text-align: center;
    padding: 24px;
    background: var(--navy);
    color: #fff;
    border: none;
}
.clock-time-display {
    font-family: 'JetBrains Mono', monospace;
    font-size: 56px;
    font-weight: 700;
    letter-spacing: 2px;
}
.clock-time-display .clock-sep { opacity: 0.4; }
.clock-date-display {
    font-size: 14px;
    color: rgba(255,255,255,0.6);
    margin-top: 4px;
}
.clock-status-badge {
    display: inline-block;
    margin-top: 12px;
    padding: 5px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    background: rgba(255,255,255,0.1);
    color: var(--gold);
    transition: background 0.3s, color 0.3s;
}
.clock-status-badge.speaking {
    background: var(--gold);
    color: var(--navy);
}

/* SCAN INPUT */
.scan-input-wrapper {
    position: relative;
    border: 2px solid var(--border);
    border-radius: 8px;
    background: #fafbfd;
    transition: 0.2s;
}
.scan-input-wrapper.focused {
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(200,151,60,0.1);
}
#uid {
    width: 100%;
    border: none;
    background: transparent;
    text-align: center;
    font-family: 'JetBrains Mono', monospace;
    font-size: 18px;
    font-weight: 600;
    color: var(--navy);
    outline: none;
    padding: 16px;
}
#uid::placeholder {
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
    font-weight: 400;
    color: var(--muted);
}
.scan-hint {
    text-align: center;
    margin-top: 10px;
    font-size: 12px;
    color: var(--muted);
}

/* RESULT */
.result-card {
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    animation: fadeIn 0.3s ease;
    margin-top: 20px;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.type-masuk { background: #ecfdf5; border: 1px solid rgba(5,150,105,0.2); }
.type-pulang { background: #eff6ff; border: 1px solid rgba(37,99,235,0.2); }
.type-duplikat { background: #fffbeb; border: 1px solid rgba(217,119,6,0.2); }
.type-error { background: #fef2f2; border: 1px solid rgba(220,38,38,0.2); }

.result-icon { font-size: 28px; margin-bottom: 10px; }
.type-masuk .result-icon { color: var(--success); }
.type-pulang .result-icon { color: var(--info); }
.type-duplikat .result-icon { color: var(--warning); }
.type-error .result-icon { color: var(--danger); }

.result-label {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    margin-bottom: 6px;
}
.type-masuk .result-label { color: var(--success); }
.type-pulang .result-label { color: var(--info); }
.type-duplikat .result-label { color: var(--warning); }
.type-error .result-label { color: var(--danger); }

.result-name { font-size: 20px; font-weight: 800; color: var(--fg); margin-bottom: 4px; }
.result-detail { font-size: 13px; color: var(--muted); }
.result-detail strong { color: var(--fg); }

.badge-status {
    display: inline-block;
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    margin-top: 8px;
}
.badge-hadir { background: rgba(5,150,105,0.1); color: var(--success); }
.badge-terlambat { background: rgba(220,38,38,0.1); color: var(--danger); }

/* SOUND TOGGLE */
.sound-toggle {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 999;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    border: none;
    background: var(--navy);
    color: var(--gold);
    font-size: 18px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s, background 0.2s;
}
.sound-toggle:hover { transform: scale(1.1); }
.sound-toggle.muted { background: #94a3b8; color: #fff; }

/* SPEAKING INDICATOR */
.speaking-wave {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    margin-left: 8px;
    vertical-align: middle;
}
.speaking-wave span {
    display: inline-block;
    width: 3px;
    background: currentColor;
    border-radius: 2px;
    animation: wave 0.6s ease-in-out infinite;
}
.speaking-wave span:nth-child(1) { height: 8px; animation-delay: 0s; }
.speaking-wave span:nth-child(2) { height: 14px; animation-delay: 0.1s; }
.speaking-wave span:nth-child(3) { height: 10px; animation-delay: 0.2s; }
.speaking-wave span:nth-child(4) { height: 16px; animation-delay: 0.3s; }
.speaking-wave span:nth-child(5) { height: 8px; animation-delay: 0.4s; }
@keyframes wave {
    0%, 100% { transform: scaleY(0.5); }
    50% { transform: scaleY(1.2); }
}

/* STATS */
.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}
.stat-item {
    padding: 16px;
    border-radius: 8px;
    text-align: center;
}
.stat-item .stat-number { font-size: 28px; font-weight: 800; line-height: 1; }
.stat-item .stat-label { font-size: 11px; color: var(--muted); margin-top: 4px; font-weight: 500; }

.stat-total { background: #fffbeb; } .stat-total .stat-number { color: var(--gold); }
.stat-hadir { background: #ecfdf5; } .stat-hadir .stat-number { color: var(--success); }
.stat-terlambat { background: #fffbeb; } .stat-terlambat .stat-number { color: var(--warning); }
.stat-belum { background: #fef2f2; } .stat-belum .stat-number { color: var(--danger); }

/* LOG TABLE */
.log-table { width: 100%; font-size: 12px; }
.log-table thead th {
    background: #f8fafc;
    padding: 10px;
    font-size: 10px;
    text-transform: uppercase;
    color: var(--muted);
    border-bottom: 2px solid var(--border);
}
.log-table tbody td { padding: 10px; border-bottom: 1px solid #f1f5f9; }

/* FOOTER */
.site-footer {
    text-align: center;
    padding: 20px;
    margin-top: 20px;
    font-size: 12px;
    color: var(--muted);
}
</style>
</head>
<body>

<!-- Top Navigation -->
<nav class="top-nav">
    <div class="nav-brand">
        <div class="nav-logos">
            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="40" height="40" rx="8" fill="#1a2d5a"/>
                <path d="M20 10L10 15L20 20L30 15L20 10Z" fill="#c8973c"/>
                <path d="M12 16V23L20 27L28 23V16" stroke="#e2b65a" stroke-width="1.5" fill="none"/>
                <path d="M20 20V27" stroke="#e2b65a" stroke-width="1.5"/>
            </svg>
            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="40" height="40" rx="8" fill="#1a2d5a"/>
                <rect x="10" y="12" width="20" height="14" rx="2" stroke="#c8973c" stroke-width="1.5" fill="none"/>
                <line x1="17" y1="26" x2="17" y2="30" stroke="#e2b65a" stroke-width="1.5"/>
                <line x1="23" y1="26" x2="23" y2="30" stroke="#e2b65a" stroke-width="1.5"/>
                <line x1="14" y1="30" x2="26" y2="30" stroke="#e2b65a" stroke-width="1.5" stroke-linecap="round"/>
                <circle cx="20" cy="19" r="3" stroke="#e2b65a" stroke-width="1.5" fill="none"/>
            </svg>
        </div>
        <div class="nav-text">
            <div class="nav-school-name">SMK BHAKTI NUSANTARA</div>
            <div class="nav-school-sub">Sistem Absensi RFID</div>
        </div>
    </div>
    <div class="nav-right">
        <a href="login.php" class="btn-login">
            <i class="fas fa-lock"></i>
            <span>Admin</span>
        </a>
    </div>
</nav>

<!-- Main Content -->
<div class="main-container">
    <!-- KOLOM KIRI -->
    <div class="left-col">
        <!-- Jam -->
        <div class="card-box clock-card" style="margin-bottom:20px;">
            <div class="clock-time-display">
                <span id="clkH">00</span><span class="clock-sep">:</span><span id="clkM">00</span><span class="clock-sep">:</span><span id="clkS">00</span>
            </div>
            <div class="clock-date-display" id="clkDate"></div>
            <div class="clock-status-badge" id="clkStatus">
                Menunggu Kartu RFID
            </div>
        </div>

        <!-- Scan RFID -->
        <div class="card-box" style="margin-bottom:0;">
            <div class="card-header-custom">
                <i class="fas fa-wifi"></i> Scan Kartu
            </div>
            <div class="card-body-custom">
                <div class="scan-input-wrapper" id="scanWrapper">
                    <form method="POST" id="formRFID">
                        <input type="text" name="uid" id="uid" placeholder="TEMPELKAN KARTU RFID DI SINI" autofocus autocomplete="off">
                    </form>
                </div>
                <div class="scan-hint">
                    <i class="fas fa-hand-pointer"></i> Arahkan kartu ke reader
                </div>
            </div>
        </div>

        <!-- Hasil Scan -->
        <div id="resultArea">
            <?php if (!empty($message_type)): ?>
            <div class="result-card type-<?= $message_type ?>">
                <div class="result-icon">
                    <?php if ($message_type === 'masuk'): ?>
                        <i class="fas fa-sign-in-alt"></i>
                    <?php elseif ($message_type === 'pulang'): ?>
                        <i class="fas fa-sign-out-alt"></i>
                    <?php elseif ($message_type === 'duplikat'): ?>
                        <i class="fas fa-exclamation-triangle"></i>
                    <?php else: ?>
                        <i class="fas fa-times-circle"></i>
                    <?php endif; ?>
                </div>
                <div class="result-label">
                    <?php
                        if ($message_type === 'masuk') echo 'ABSEN MASUK';
                        elseif ($message_type === 'pulang') echo 'ABSEN PULANG';
                        elseif ($message_type === 'duplikat') echo 'SUDAH ABSEN';
                        else echo 'KARTU TIDAK DIKENALI';
                    ?>
                </div>
                <?php if ($message_type === 'masuk'): ?>
                    <div class="result-name"><?= htmlspecialchars($msg_nama) ?></div>
                    <div class="result-detail">
                        Kelas : <strong><?= htmlspecialchars($msg_kelas) ?></strong> | Jam : <strong><?= htmlspecialchars($msg_jam) ?></strong>
                    </div>
                    <div class="badge-status <?= strtolower($msg_status) ?>">
                        <?= $msg_status === 'Terlambat' ? 'TERLAMBAT' : 'HADIR' ?>
                    </div>
                <?php elseif ($message_type === 'pulang'): ?>
                    <div class="result-name"><?= htmlspecialchars($msg_nama) ?></div>
                    <div class="result-detail">
                        Kelas : <strong><?= htmlspecialchars($msg_kelas) ?></strong> | Jam : <strong><?= htmlspecialchars($msg_jam) ?></strong>
                    </div>
                <?php elseif ($message_type === 'duplikat'): ?>
                    <div class="result-name"><?= htmlspecialchars($msg_nama) ?></div>
                    <div class="result-detail">Sudah melakukan absen masuk & pulang</div>
                <?php else: ?>
                    <div class="result-detail">Kartu RFID tidak terdaftar!</div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- KOLOM KANAN -->
    <div class="right-col">
        <!-- Statistik -->
        <div class="card-box" style="margin-bottom:20px;">
            <div class="card-header-custom">
                <i class="fas fa-chart-bar"></i> Rekap Hari Ini
            </div>
            <div class="card-body-custom">
                <div class="stats-grid">
                    <div class="stat-item stat-total">
                        <div class="stat-number"><?= $totalSiswa ?></div>
                        <div class="stat-label">Total Siswa</div>
                    </div>
                    <div class="stat-item stat-hadir">
                        <div class="stat-number"><?= $hadirHariIni ?></div>
                        <div class="stat-label">Hadir</div>
                    </div>
                    <div class="stat-item stat-terlambat">
                        <div class="stat-number"><?= $terlambatHariIni ?></div>
                        <div class="stat-label">Terlambat</div>
                    </div>
                    <div class="stat-item stat-belum">
                        <div class="stat-number"><?= $belumAbsen ?></div>
                        <div class="stat-label">Belum Absen</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Log Terbaru -->
        <div class="card-box">
            <div class="card-header-custom">
                <i class="fas fa-list-ol"></i> Riwayat Terbaru
            </div>
            <div style="padding:0;">
                <?php if ($logQuery && mysqli_num_rows($logQuery) > 0): ?>
                <table class="log-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Kelas</th>
                            <th>Masuk</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($logQuery)): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
                            <td><?= isset($row['kelas']) ? htmlspecialchars($row['kelas']) : '-' ?></td>
                            <td style="font-family:'JetBrains Mono',monospace;font-size:11px;"><?= htmlspecialchars($row['jam_masuk']) ?></td>
                            <td>
                                <span class="badge-status <?= $row['status'] === 'Hadir' ? 'badge-hadir' : 'badge-terlambat' ?>"><?= htmlspecialchars($row['status']) ?></span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div style="text-align:center; padding:30px; color:var(--muted); font-size:13px;">
                    Belum ada data absensi hari ini
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Tombol Sound Toggle -->
<button class="sound-toggle" id="soundToggle" title="Aktifkan/Matikan Suara">
    <i class="fas fa-volume-up" id="soundIcon"></i>
</button>

<!-- Footer -->
<div class="site-footer">
    &copy; <?= date("Y") ?> BHATARA INFORMATION TECHNOLOGY &bull; V2.0
</div>

<script>
// ============================================
// KONFIGURASI SUARA
// ============================================
let soundEnabled = true;

// Cek preferensi dari localStorage
if (localStorage.getItem('rfid_sound') === 'off') {
    soundEnabled = false;
}

// Toggle tombol suara
const soundToggle = document.getElementById('soundToggle');
const soundIcon = document.getElementById('soundIcon');

function updateSoundUI() {
    if (soundEnabled) {
        soundToggle.classList.remove('muted');
        soundIcon.className = 'fas fa-volume-up';
    } else {
        soundToggle.classList.add('muted');
        soundIcon.className = 'fas fa-volume-mute';
    }
}
updateSoundUI();

soundToggle.addEventListener('click', function() {
    soundEnabled = !soundEnabled;
    localStorage.setItem('rfid_sound', soundEnabled ? 'on' : 'off');
    updateSoundUI();
});

// ============================================
// WEB AUDIO API - BEEP NOTIFIKASI
// ============================================
const AudioCtx = window.AudioContext || window.webkitAudioContext;
let audioCtx = null;

function getAudioCtx() {
    if (!audioCtx) {
        audioCtx = new AudioCtx();
    }
    return audioCtx;
}

// Beep sukses (nada naik - hadir)
function playBeepSuccess() {
    if (!soundEnabled) return;
    try {
        const ctx = getAudioCtx();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = 'sine';
        osc.frequency.setValueAtTime(523, ctx.currentTime);       // C5
        osc.frequency.setValueAtTime(659, ctx.currentTime + 0.12); // E5
        osc.frequency.setValueAtTime(784, ctx.currentTime + 0.24); // G5
        gain.gain.setValueAtTime(0.3, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.5);
    } catch(e) {}
}

// Beep peringatan (nada turun - terlambat)
function playBeepWarning() {
    if (!soundEnabled) return;
    try {
        const ctx = getAudioCtx();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(587, ctx.currentTime);       // D5
        osc.frequency.setValueAtTime(440, ctx.currentTime + 0.2); // A4
        gain.gain.setValueAtTime(0.35, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.5);
    } catch(e) {}
}

// Beep error (nada rendah berulang - kartu tidak dikenali)
function playBeepError() {
    if (!soundEnabled) return;
    try {
        const ctx = getAudioCtx();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(220, ctx.currentTime);
        osc.frequency.setValueAtTime(180, ctx.currentTime + 0.15);
        osc.frequency.setValueAtTime(220, ctx.currentTime + 0.3);
        gain.gain.setValueAtTime(0.2, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.5);
    } catch(e) {}
}

// Beep info (nada lembut - pulang)
function playBeepInfo() {
    if (!soundEnabled) return;
    try {
        const ctx = getAudioCtx();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = 'sine';
        osc.frequency.setValueAtTime(659, ctx.currentTime);       // E5
        osc.frequency.setValueAtTime(784, ctx.currentTime + 0.15); // G5
        gain.gain.setValueAtTime(0.25, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.4);
    } catch(e) {}
}

// Beep duplikat (nada ganda - sudah absen)
function playBeepDuplicate() {
    if (!soundEnabled) return;
    try {
        const ctx = getAudioCtx();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(440, ctx.currentTime);
        osc.frequency.setValueAtTime(440, ctx.currentTime + 0.15);
        gain.gain.setValueAtTime(0.25, ctx.currentTime);
        gain.gain.setValueAtTime(0, ctx.currentTime + 0.1);
        gain.gain.setValueAtTime(0.25, ctx.currentTime + 0.15);
        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.4);
    } catch(e) {}
}

// ============================================
// WEB SPEECH API - PESAN SUARA
// ============================================
function speak(text) {
    if (!soundEnabled) return;
    if (!('speechSynthesis' in window)) return;

    // Batalkan antrian sebelumnya
    window.speechSynthesis.cancel();

    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'id-ID';
    utterance.rate = 0.95;
    utterance.pitch = 1.05;
    utterance.volume = 1;

    // Cari suara Bahasa Indonesia jika tersedia
    const voices = window.speechSynthesis.getVoices();
    const idVoice = voices.find(v => v.lang.startsWith('id'));
    if (idVoice) {
        utterance.voice = idVoice;
    }

    // Indikator sedang berbicara
    const badge = document.getElementById('clkStatus');
    utterance.onstart = function() {
        if (badge) {
            badge.classList.add('speaking');
            badge.innerHTML = '<i class="fas fa-volume-up"></i> Berbicara... <span class="speaking-wave"><span></span><span></span><span></span><span></span><span></span></span>';
        }
    };
    utterance.onend = function() {
        if (badge) {
            badge.classList.remove('speaking');
            badge.textContent = 'Menunggu Kartu RFID';
        }
    };

    window.speechSynthesis.speak(utterance);
}

// Pastikan voices sudah dimuat
if ('speechSynthesis' in window) {
    window.speechSynthesis.getVoices();
    window.speechSynthesis.onvoiceschanged = function() {
        window.speechSynthesis.getVoices();
    };
}

// ============================================
// JAM REALTIME
// ============================================
const hariId = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
const bulanId = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

function updateClock() {
    const now = new Date();
    document.getElementById('clkH').textContent = String(now.getHours()).padStart(2,'0');
    document.getElementById('clkM').textContent = String(now.getMinutes()).padStart(2,'0');
    document.getElementById('clkS').textContent = String(now.getSeconds()).padStart(2,'0');
    document.getElementById('clkDate').textContent = hariId[now.getDay()] + ', ' + now.getDate() + ' ' + bulanId[now.getMonth()] + ' ' + now.getFullYear();
}
setInterval(updateClock, 1000);
updateClock();

// ============================================
// FOCUS HANDLER
// ============================================
const uidInput = document.getElementById('uid');
const scanWrapper = document.getElementById('scanWrapper');

document.addEventListener('click', () => setTimeout(() => uidInput.focus(), 50));

uidInput.addEventListener('focus', () => scanWrapper.classList.add('focused'));
uidInput.addEventListener('blur', () => scanWrapper.classList.remove('focused'));

uidInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('formRFID').submit();
    }
});

// ============================================
// EKSEKUSI SUARA SAAT HASIL SCAN ADA
// ============================================
<?php if (!empty($message_type)): ?>

// Delay sedikit agar browser sudah siap
setTimeout(function() {
    <?php if ($message_type === 'masuk'): ?>
        // Absen Masuk - Hadir
        <?php if ($msg_status === 'Hadir'): ?>
            playBeepSuccess();
            setTimeout(function() {
                speak('Selamat datang, <?= addslashes($msg_nama) ?>. Absen masuk dicatat. Hadir.');
            }, 500);
        // Absen Masuk - Terlambat
        <?php else: ?>
            playBeepWarning();
            setTimeout(function() {
                speak('<?= addslashes($msg_nama) ?>. Absen masuk dicatat. Terlambat.');
            }, 500);
        <?php endif; ?>

    <?php elseif ($message_type === 'pulang'): ?>
        // Absen Pulang
        playBeepInfo();
        setTimeout(function() {
            speak('Selamat pulang, <?= addslashes($msg_nama) ?>. Absen pulang dicatat.');
        }, 400);

    <?php elseif ($message_type === 'duplikat'): ?>
        // Duplikat
        playBeepDuplicate();
        setTimeout(function() {
            speak('<?= addslashes($msg_nama) ?> sudah melakukan absensi hari ini.');
        }, 400);

    <?php elseif ($message_type === 'error'): ?>
        // Kartu tidak dikenali
        playBeepError();
        setTimeout(function() {
            speak('Kartu tidak dikenali. Silakan hubungi administrator.');
        }, 400);
    <?php endif; ?>
}, 300);

// Auto hide hasil scan setelah 6 detik
setTimeout(function() {
    const resultArea = document.getElementById('resultArea');
    const card = resultArea.querySelector('.result-card');
    if (card) {
        card.style.transition = 'opacity 0.3s ease';
        card.style.opacity = '0';
        setTimeout(function() {
            resultArea.innerHTML = '';
            uidInput.value = '';
            uidInput.focus();
        }, 300);
    }
}, 6000);

<?php endif; ?>
</script>
</body>
</html>