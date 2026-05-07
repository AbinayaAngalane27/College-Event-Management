<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rejouir - Event Categories</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
</head>
<body id="event-categories">
    <nav>
        <a href="index.php">Home</a>
        <a href="event_categories.php">Events</a>
        <a href="login.php">Login</a>
        <a href="contact.php">Contact</a>
    </nav>

    <main>
        <h2>Select an Event Category</h2>

        <div class="carousel">
            <div class="carousel-track-container">
                <div class="carousel-track">
                    <!-- Technical Events -->
                    <div class="event-category">
                        <a href="technical-events.php">
                            <img src="img/technical.png" alt="Technical Events">
                            <div class="event-info">
                                <h3>Technical Events</h3>
                            </div>
                        </a>
                    </div>
                    <!-- Cultural Events -->
                    <div class="event-category">
                        <a href="cultural-events.php">
                            <img src="img/cultural.png" alt="Cultural Events">
                            <div class="event-info">
                                <h3>Cultural Events</h3>
                            </div>
                        </a>
                    </div>
                    <!-- Sports Events -->
                    <div class="event-category">
                        <a href="sports-events.php">
                            <img src="img/sports.png" alt="Sports Events">
                            <div class="event-info">
                                <h3>Sports Events</h3>
                            </div>
                        </a>
                    </div>
                    <!-- Gaming Events -->
                    <div class="event-category">
                        <a href="gaming-events.php">
                            <img src="img/gaming.png" alt="Gaming Events">
                            <div class="event-info">
                                <h3>Gaming Events</h3>
                            </div>
                        </a>
                    </div>
                    <!-- Literary Events -->
                    <div class="event-category">
                        <a href="literary-events.php">
                            <img src="img/literary.png" alt="Literary Events">
                            <div class="event-info">
                                <h3>Literary Events</h3>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer-animated">
        <p>&copy; 2K25 Rejouir. All rights reserved.</p>
    </footer>

    <script>
        $(document).ready(function() {
            const track = $('.carousel-track');
            const slides = $('.event-category');
            const slideWidth = slides.outerWidth(true);
            const trackWidth = slideWidth * slides.length;
            
            track.css('width', trackWidth + 'px');

            let currentPosition = 0;

            function autoSlide() {
                currentPosition -= slideWidth;
                if (currentPosition < -trackWidth + slideWidth * 2) {
                    currentPosition = 0;
                }
                track.css('transform', `translateX(${currentPosition}px)`);
            }

            setInterval(autoSlide, 3000);

            $(".event-category").hide().fadeIn(1000);

            $(".event-info h3").hover(function() {
                $(this).effect("shake", { distance: 5, times: 2 }, 300);
                $(this).css("text-shadow", "0 0 10px #00FF7F");
            }, function() {
                $(this).css("text-shadow", "none");
            });

            $(".event-category img").hover(function() {
                $(this).css("transform", "scale(1.2)").css("transition", "transform 0.3s ease");
            }, function() {
                $(this).css("transform", "scale(1)");
            });
        });
    </script>
</body>
</html>
