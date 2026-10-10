@extends('website.layouts.app')

@section('title', 'Click2Serve - Home')

@section('content')

    <!-- Hero Section -->
    <div class="hero">
        <img src="{{ asset('img/new_bg_no_copyright.jpg') }}" alt="Image not found">
    </div>

    <!-- Features Section -->
    <section id="home">
        <div class="features-layout">
            {{-- left column --}}
            <div class="left-col">
                <div class="feature-card">
                    <table cellspacing="10">
                        <tr data-aos="fade-right">
                            <td><img src="{{ asset('img/map_icon.png') }}" alt="Map"></td>
                            <td>
                                <h3>3D Map</h3>
                                <p>Explore a 3D map of the Municipal Hall to help you find your way around.</p>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="feature-card">
                    <table cellspacing="10">
                        <tr data-aos="fade-right">
                            <td><img src="{{ asset('img/service_request_icon.png') }}" alt="Service Request"></td>
                            <td>
                                <h3>Service Request</h3>
                                <p>Request a service online or through our Click2Serve kiosk.</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- center kiosk --}}
            <div class="center-col" data-aos="fade-up">
                <img src="{{ asset('img/Kiosk.png') }}" alt="Kiosk" class="kiosk-img">
            </div>

            {{-- right column --}}
            <div class="right-col">
                <div class="feature-card">
                    <table cellspacing="10">
                        <tr data-aos="fade-left">
                            <td><img src="{{ asset('img/track_request_icon.png') }}" alt="Track"></td>
                            <td>
                                <h3>Track Request</h3>
                                <p>Track the progress of your service requests easily.</p>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="feature-card">
                    <table cellspacing="10">
                        <tr data-aos="fade-left">
                            <td><img src="{{ asset('img/municipal_info_icon.png') }}" alt="Info"></td>
                            <td>
                                <h3>Municipal Information</h3>
                                <p>Get useful information about the Municipal Hall and its services.</p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
        <h2>About</h2>
        <div class="about-layout">
            <div class="carousel">
                <div class="slides">
                    @if($about && $about->image)
                        <img src="{{ asset($about->image) }}" alt="{{ $about->title }}">
                    @else
                        <img src="{{ asset('img/carousel_img.png') }}" alt="Carousel 1">
                    @endif
                    <img src="{{ asset('img/Kiosk.png') }}" alt="Carousel 2">
                    <img src="{{ asset('img/carousel_img.png') }}" alt="Carousel 3">
                </div>
            </div>
            <div class="about-content">
                @if($about)
                    <div class="about-text-collapsed">
                        <p>
                            {{ Str::limit(strip_tags($about->content), 150) }}...
                        </p>
                        <button class="read-more-btn" onclick="toggleAboutContent()">
                            Read more <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="about-text-expanded" id="aboutExpanded">
                        <div>
                            {!! $about->content !!}
                        </div>
                        <button class="read-less-btn" onclick="toggleAboutContent()">
                            Show less <i class="fas fa-chevron-up"></i>
                        </button>
                    </div>
                @else
                    <div class="about-text-collapsed">
                        <p>
                            The Click2Serve: Kiosk and Online Platform for Municipal Services Request in Alaminos City is a
                            transformative solution designed to improve public access to municipal services...
                        </p>
                        <button class="read-more-btn" onclick="toggleAboutContent()">
                            Read more <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="about-text-expanded" id="aboutExpanded">
                        <p>
                            The Click2Serve: Kiosk and Online Platform for Municipal Services Request in Alaminos City is a
                            transformative solution designed to improve public access to municipal services. This innovative
                            system combines physical kiosk stations located at the Municipal Hall with a comprehensive online
                            platform, enabling citizens to request, track, and manage various government services efficiently.
                        </p>
                        <button class="read-less-btn" onclick="toggleAboutContent()">
                            Show less <i class="fas fa-chevron-up"></i>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <script>
        function toggleAboutContent() {
            const expanded = document.getElementById('aboutExpanded');
            const collapsed = document.querySelector('.about-text-collapsed');

            if (expanded.classList.contains('show')) {
                expanded.classList.remove('show');
                collapsed.style.display = 'block';
                // Smooth scroll to top of about section
                document.getElementById('about').scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                collapsed.style.display = 'none';
                expanded.classList.add('show');
            }
        }
    </script>

    <!-- Blog Section -->
    <section id="blog">
        <h2>Blog</h2>
        <p>The Click2Serve Blog is the best place to read about the latest news, insights, trends and more inside of
            Alaminos City.</p>
        <div class="blog">
            @forelse($blogs as $blog)
                <div class="blog-card" onclick="openBlogModal({{ $blog->id }})">
                    @if($blog->image)
                        <img src="{{ asset($blog->image) }}" alt="{{ $blog->title }}">
                    @else
                        <img src="{{ asset('img/mayor.png') }}" alt="{{ $blog->title }}">
                    @endif
                    <div class="content">
                        <h3>{{ $blog->title }}</h3>
                        <p>{{ Str::limit(strip_tags($blog->content), 80) }}</p>
                        @if($blog->author)
                            <small style="display: block; color: #666; margin-top: 8px;">By {{ $blog->author }}</small>
                        @endif
                        @if($blog->published_at)
                            <small style="display: block; color: #666;">{{ \Carbon\Carbon::parse($blog->published_at)->format('M d, Y') }}</small>
                        @endif
                        <a href="javascript:void(0)" class="read-more-link">Read more →</a>
                    </div>
                </div>
            @empty
                <div class="blog-card">
                    <img src="{{ asset('img/mayor.png') }}" alt="City Mayor">
                    <div class="content">
                        <h3>Message of the City Mayor</h3>
                        <p>Welcoming Guests in Alaminos City</p>
                        <a href="javascript:void(0)" class="read-more-link">Read more →</a>
                    </div>
                </div>
                <div class="blog-card">
                    <img src="{{ asset('img/alaminos_banner.png') }}" alt="Hundred Islands">
                    <div class="content">
                        <h3>Alaminos City Economy</h3>
                        <p>Known for its progressing economy, Hundred Islands...</p>
                        <a href="javascript:void(0)" class="read-more-link">Read more →</a>
                    </div>
                </div>
                <div class="blog-card">
                    <img src="{{ asset('img/officials.png') }}" alt="Government">
                    <div class="content">
                        <h3>Solid Partnership</h3>
                        <p>The city government of Alaminos, in solid partnership...</p>
                        <a href="javascript:void(0)" class="read-more-link">Read more →</a>
                    </div>
                </div>
            @endforelse
        </div>
        <div class="center-btn">
            <a href="http://alaminoscity.gov.ph/index.html" class="btn" target="_blank">Go to Alaminos Website</a>
        </div>
        <p class="ctto-notice">Content and images: Credits to the City Government of Alaminos. <a
                href="http://alaminoscity.gov.ph" target="_blank">alaminoscity.gov.ph</a></p>
    </section>

    <!-- Blog Modal -->
    <div id="blogModal" class="blog-modal">
        <div class="blog-modal-content">
            <div class="blog-modal-header">
                <h2 id="blogModalTitle">Blog Title</h2>
                <button class="blog-modal-close" onclick="closeBlogModal()">&times;</button>
            </div>
            <div class="blog-modal-body">
                <img id="blogModalImage" src="" alt="Blog Image" class="blog-modal-image">
                <div id="blogModalContent" class="blog-modal-text"></div>
            </div>
            <div class="blog-modal-footer">
                <a id="blogModalLink" href="http://alaminoscity.gov.ph/index.html" target="_blank" class="btn">Visit
                    Official Website</a>
                <button onclick="closeBlogModal()" class="btn btn-secondary">Close</button>
            </div>
        </div>
    </div>

    <script>
        // Blog data from database
        const blogData = {
            @foreach($blogs as $blog)
            {{ $blog->id }}: {
                title: {!! json_encode($blog->title) !!},
                image: "{{ $blog->image ? asset($blog->image) : asset('img/mayor.png') }}",
                content: {!! json_encode($blog->content) !!},
                author: {!! json_encode($blog->author ?? '') !!},
                publishedAt: {!! json_encode($blog->published_at ? \Carbon\Carbon::parse($blog->published_at)->format('M d, Y') : '') !!}
            },
            @endforeach
            // Fallback blog data
            mayor: {
                title: "Message of the City Mayor",
                image: "{{ asset('img/mayor.png') }}",
                content: `<h3>Welcome to Alaminos City</h3><p><strong>Hon. Arth Bryan C. Celeste</strong><br>City Mayor</p><p>Greetings from the City of Alaminos, Pangasinan – the Gateway to the Famous Hundred Islands National Park.</p>`,
                link: "http://alaminoscity.gov.ph/discover-alaminos/message-mayor-arth.html"
            },
            economy: {
                title: "Alaminos City Economy & Tourism",
                image: "{{ asset('img/alaminos_banner.png') }}",
                content: `<h3>Hundred Islands National Park</h3><p>Alaminos City is home to the world-famous <strong>Hundred Islands National Park</strong>.</p>`,
                link: "http://alaminoscity.gov.ph/i-choose-hundred-islands/hundred-islands-national-park.html"
            },
            partnership: {
                title: "Solid Partnership – City Government",
                image: "{{ asset('img/officials.png') }}",
                content: `<h3>City Government of Alaminos</h3><p>The City Government of Alaminos is committed to providing excellent public services.</p>`,
                link: "http://alaminoscity.gov.ph/public-service/city-offices.html"
            }
        };

        function openBlogModal(blogId) {
            const blog = blogData[blogId];
            if (!blog) return;

            document.getElementById('blogModalTitle').textContent = blog.title;
            document.getElementById('blogModalImage').src = blog.image || "{{ asset('img/mayor.png') }}";
            document.getElementById('blogModalContent').innerHTML = blog.content;
            
            // Add author and date info if available
            if (blog.author || blog.publishedAt) {
                let metaInfo = '<div style="margin-bottom: 20px; padding: 10px; background: #f8f9fa; border-radius: 8px;">';
                if (blog.author) {
                    metaInfo += '<p style="margin: 5px 0;"><strong>Author:</strong> ' + blog.author + '</p>';
                }
                if (blog.publishedAt) {
                    metaInfo += '<p style="margin: 5px 0;"><strong>Published:</strong> ' + blog.publishedAt + '</p>';
                }
                metaInfo += '</div>';
                document.getElementById('blogModalContent').innerHTML = metaInfo + blog.content;
            }
            
            // Update link if it exists, otherwise hide it
            const linkElement = document.getElementById('blogModalLink');
            if (blog.link) {
                linkElement.href = blog.link;
                linkElement.style.display = 'inline-block';
            } else {
                linkElement.style.display = 'none';
            }
            
            document.getElementById('blogModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeBlogModal() {
            document.getElementById('blogModal').classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        // Close modal on outside click
        document.getElementById('blogModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeBlogModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeBlogModal();
            }
        });
    </script>

    <!-- Municipal Information & Announcements Section -->
    <section id="municipal-info">
        <div class="municipal-container">
            @if(isset($announcements) && $announcements->count() > 0)
                <div class="announcements-header-main" data-aos="fade-up">
                    <h2>Announcements</h2>
                </div>
                <div class="announcements-grid">
                    @foreach($announcements as $announcement)
                        <div class="announcement-card" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                            @if($announcement->image)
                                <div class="announcement-image">
                                    <img src="{{ asset($announcement->image) }}" alt="{{ $announcement->title ?? 'Announcement' }}">
                                </div>
                            @endif
                            <div class="announcement-content">
                                @if($announcement->title)
                                    <h3>{{ $announcement->title }}</h3>
                                @endif
                                <div class="announcement-text">
                                    {!! $announcement->content !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="official-website-link">
                    <a href="https://www.facebook.com/cityofalaminos.pangasinan" target="_blank" rel="noopener noreferrer">
                        See Official Facebook Page for More Information
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            @else
                <div class="announcements-wrapper" data-aos="fade-up">
                    <div class="announcements-header">
                        <h2><i class="fas fa-bullhorn"></i> Announcements</h2>
                    </div>
                    <div class="announcements-empty">
                        <p>No announcements at this time.</p>
                    </div>
                    <div class="official-website-link">
                        <a href="https://www.alaminoscity.gov.ph/index.html" target="_blank" rel="noopener noreferrer">
                            See Official Website for More Information
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <style>
        #municipal-info {
            padding: 60px 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .municipal-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .announcements-header-main {
            text-align: center;
            margin-bottom: 40px;
        }

        .announcements-header-main h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #008080;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin: 0;
        }

        .announcements-header-main h2 i {
            font-size: 2rem;
        }

        .announcements-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .announcement-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .announcement-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .announcement-image {
            width: 100%;
            height: 200px;
            overflow: hidden;
            background: #f8f9fa;
        }

        .announcement-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .announcement-content {
            padding: 24px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .announcement-content h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #008080;
            margin: 0 0 16px 0;
        }

        .announcement-text {
            color: #334155;
            line-height: 1.8;
            font-size: 1rem;
            flex: 1;
        }

        .announcement-text h3,
        .announcement-text h4 {
            color: #008080;
            margin-top: 16px;
            margin-bottom: 8px;
            font-size: 1.2rem;
        }

        .announcement-text h3:first-child,
        .announcement-text h4:first-child {
            margin-top: 0;
        }

        .announcement-text ul,
        .announcement-text ol {
            margin: 12px 0;
            padding-left: 24px;
        }

        .announcement-text li {
            margin: 8px 0;
        }

        .announcement-text p {
            margin: 12px 0;
        }

        .announcements-wrapper {
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .announcements-header {
            background: linear-gradient(135deg, #008080 0%, #00A0A0 100%);
            padding: 24px 32px;
            color: white;
        }

        .announcements-header h2 {
            margin: 0;
            font-size: 1.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .announcements-header h2 i {
            font-size: 1.5rem;
        }

        .announcements-empty {
            padding: 48px 32px;
            text-align: center;
            color: #64748b;
            font-size: 1.1rem;
        }

        .official-website-link {
            padding: 32px;
            text-align: center;
            margin-top: 32px;
        }

        .official-website-link a {
            color: #008080;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            padding: 12px 24px;
            border-radius: 8px;
            border: 2px solid #008080;
            background: white;
        }

        .official-website-link a:hover {
            background: #008080;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 128, 128, 0.3);
        }

        .official-website-link a i {
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            #municipal-info {
                padding: 40px 16px;
            }

            .announcements-header-main h2 {
                font-size: 2rem;
            }

            .announcements-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .announcement-content {
                padding: 20px;
            }

            .official-website-link {
                padding: 24px 16px;
            }

            .official-website-link a {
                font-size: 0.9rem;
                padding: 10px 20px;
            }
        }
    </style>

    <!-- Services Section -->
    <section id="services">
        <h2>Our Services</h2>
        <div class="services">
            <a href="{{ route('website.services.civil-registry') }}" class="service-box">
                <img src="{{ asset('img/civil_icon.png')}}" alt="Civil">
                <h4>City Civil Registration Services</h4>
            </a>
            <a href="{{ route('website.services.transportation') }}" class="service-box">
                <img src="{{ asset('img/trans_icon.png')}}" alt="Transport">
                <h4>City Transportation Regulation Unit</h4>
            </a>
            <a href="{{ route('website.services.business-permit') }}" class="service-box">
                <img src="{{ asset('img/business_icon.png')}}" alt="Business">
                <h4>Business Permit & Licensing</h4>
            </a>
            <a href="{{ route('website.services.veterinary') }}" class="service-box">
                <img src="{{ asset('img/agri_icon.png')}}" alt="Veterinary">
                <h4>City Veterinary Office</h4>
            </a>
            <a href="{{ route('website.services.engineering') }}" class="service-box">
                <img src="{{ asset('img/eng_icon.png')}}" alt="Engineering">
                <h4>Engineering Office</h4>
            </a>
        </div>
    </section>


@endsection