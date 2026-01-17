<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>OutdoorKriss - Pilih Cabang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f7f3ee;
            color: #333;
        }

        /* NAVBAR */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 40px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: #c86b00;
            font-size: 18px;
        }

        .brand img {
            width: 38px;
        }

        .nav-right {
            font-size: 14px;
            color: #c86b00;
            cursor: pointer;
        }

        /* HEADER */
        .hero {
            text-align: center;
            margin: 50px 0 30px;
        }

        .hero h1 {
            color: #8b3f00;
            font-size: 26px;
            margin-bottom: 8px;
        }

        .hero p {
            font-size: 14px;
            color: #666;
        }

        .hero small {
            display: block;
            margin-top: 5px;
            color: #c86b00;
        }

        /* CARD GRID */
        .container {
            width: 90%;
            max-width: 1100px;
            margin: auto;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .card {
            background: #fff;
            border-radius: 14px;
            padding: 18px;
            border: 1px solid #eee;
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        }

        .card-title {
            background: #c86b00;
            color: #fff;
            text-align: center;
            padding: 8px;
            border-radius: 20px;
            font-size: 14px;
            margin-bottom: 14px;
            font-weight: 500;
        }

        .info {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #555;
            margin-bottom: 10px;
        }

        .info i {
            color: #c86b00;
            width: 18px;
        }

        
        /* PAGINATION */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 40px;
            font-size: 14px;
            color: #c86b00;
        }

        .pagination span,
        .pagination a {
            cursor: pointer;
            padding: 4px 8px;
        }

        .pagination .active {
            font-weight: 600;
            border-bottom: 2px solid #c86b00;
        }

        @media (max-width: 768px) {
            .navbar {
                padding: 14px 20px;
            }
            .hero h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <div class="navbar">
        <div class="brand">
            <img src="assets/images/logo.png">
            OutdoorKriss
        </div>
        <div class="nav-right">
            Penyewa <i class="fa-solid fa-chevron-down"></i>
        </div>
    </div>

    <!-- HERO -->
    <div class="hero">
        <h1>Selamat Datang Di OutdoorKriss</h1>
        <p>Penyewaan Perlengkapan terpercaya di Banyuwangi</p>
        <small>Silakan pilih cabang di bawah ini untuk melanjutkan.</small>
    </div>

    <!-- CONTENT -->
    <div class="container">
            <!-- CARD -->
            <div class="grid">
    @foreach ($cabang as $c)
        <div class="card">
            <div class="card-title">{{ $c->nama_cabang }}</div>
            <div class="info">
                <i class="fa-solid fa-location-dot"></i>
                {{ $c->lokasi }}
            </div>
            <div class="info">
                <i class="fa-brands fa-whatsapp"></i>
                {{ $c->no_telepon ?? '082331077579' }}
            </div>
        </div>
    @endforeach
</div>

        <!-- PAGINATION -->
        <div class="pagination">
            <span>&laquo;</span>
            <span class="active">1</span>
            <span>2</span>
            <span>3</span>
            <span>4</span>
            <span>5</span>
            <span>...</span>
            <span>&raquo;</span>
        </div>
    </div>

</body>
</html>
