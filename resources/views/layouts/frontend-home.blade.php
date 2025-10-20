<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookHive - Your Digital Library</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }
        .line-clamp-2 { 
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-clamp: 2;
        }
        body {
            background: #fafafa;
        }
    </style>
</head>
<body>
    
    @include('layouts.navbars.main-navbar')

    <main>
        @yield('content')
    </main>

    <footer style="background: #1F2937; color: white; padding: 60px 0 30px; margin-top: 80px;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 24px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px;">
                <div>
                    <h3 style="font-size: 20px; font-weight: 700; margin-bottom: 16px; color: white;">BookHive</h3>
                    <p style="color: #9CA3AF; line-height: 1.6; font-size: 14px;">Your digital library where every book finds its reader.</p>
                </div>
                <div>
                    <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: white;">Navigation</h4>
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <a href="{{ route('home') }}" style="color: #9CA3AF; text-decoration: none; font-size: 14px; transition: color 0.2s ease;" onmouseover="this.style.color='white'">Home</a>
                        <a href="{{ route('about') }}" style="color: #9CA3AF; text-decoration: none; font-size: 14px; transition: color 0.2s ease;" onmouseover="this.style.color='white'">About</a>
                        <a href="{{ route('contact') }}" style="color: #9CA3AF; text-decoration: none; font-size: 14px; transition: color 0.2s ease;" onmouseover="this.style.color='white'">Contact</a>
                    </div>
                </div>
                <div>
                    <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: white;">Contact</h4>
                    <p style="color: #9CA3AF; margin-bottom: 8px; font-size: 14px;">contact@bookhive.com</p>
                    <p style="color: #9CA3AF; font-size: 14px;">+33 1 23 45 67 89</p>
                </div>
            </div>
            <div style="border-top: 1px solid #374151; margin-top: 50px; padding-top: 30px; text-align: center; color: #6B7280; font-size: 14px;">
                <p>&copy; 2024 BookHive. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>