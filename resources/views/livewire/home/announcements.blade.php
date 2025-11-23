<div class="w-full mx-auto">
  <x-homepage-header id="announcements">
    <div>
      <h1 class="font-semibold">Announcements</h1>
      <p>
        Stay updated with the latest news and announcements from HealthCardGo.
      </p>
    </div>
  </x-homepage-header>

  <flux:container class="my-10">
    <section class="newsfeed">
      <article class="newsfeed-card">
        <i class="ri-megaphone-line newsfeed-icon"></i>
        <div class="newsfeed-content">
          <h3 class="newsfeed-title" data-key="announcement1_title">Free Health Seminar This Weekend</h3>
          <p class="newsfeed-description" data-key="announcement1_description">
            Join our free health seminar on June 8, 2025, at the City Hall Auditorium. Topics include nutrition,
            hygiene, and disease prevention. All are welcome!
          </p>
          <time class="newsfeed-date" data-key="announcement1_date" datetime="2025-06-04">Posted: June 4, 2025</time>
        </div>
      </article>
      <article class="newsfeed-card">
        <i class="ri-shield-cross-line newsfeed-icon"></i>
        <div class="newsfeed-content">
          <h3 class="newsfeed-title" data-key="announcement2_title">Vaccination Drive Extended</h3>
          <p class="newsfeed-description" data-key="announcement2_description">
            Due to high demand, our free vaccination drive has been extended until June 15, 2025. Visit your nearest
            health center for your shot!
          </p>
          <time class="newsfeed-date" data-key="announcement2_date" datetime="2025-06-03">Posted: June 3, 2025</time>
        </div>
      </article>
      <article class="newsfeed-card">
        <i class="ri-information-line newsfeed-icon"></i>
        <div class="newsfeed-content">
          <h3 class="newsfeed-title" data-key="announcement3_title">New Features on HealthCardGo</h3>
          <p class="newsfeed-description" data-key="announcement3_description">
            We have launched new features including appointment booking, digital health records, and a health heat map.
            Explore them now in your dashboard!
          </p>
          <time class="newsfeed-date" data-key="announcement3_date" datetime="2025-06-01">Posted: June 1, 2025</time>
        </div>
      </article>
      <button class="load-more-btn" data-key="load_more_button" onclick="loadMore()">Load More</button>
    </section>
  </flux:container>
</div>

@push('scripts')
    <script>
    // 2. Define Translations in JavaScript
    const translations = {
      english: {
        // Navigation (common across pages)
        nav_home: "Home",
        nav_about_us: "About Us",
        nav_services: "Services",
        nav_heat_map: "Heat Map",
        nav_blog: "Blog",
        nav_announce:"Announcement",
        announcements_page_title: "Announcements | HealthCardGo",

        // Announcements Page Specific - Static Content
        announcement1_title: "Free Health Seminar This Weekend",
        announcement1_description: "Join our free health seminar on June 8, 2025, at the City Hall Auditorium. Topics include nutrition, hygiene, and disease prevention. All are welcome!",
        announcement1_date: "Posted: June 4, 2025",

        announcement2_title: "Vaccination Drive Extended",
        announcement2_description: "Due to high demand, our free vaccination drive has been extended until June 15, 2025. Visit your nearest health center for your shot!",
        announcement2_date: "Posted: June 3, 2025",

        announcement3_title: "New Features on HealthCardGo",
        announcement3_description: "We have launched new features including appointment booking, digital health records, and a health heat map. Explore them now in your dashboard!",
        announcement3_date: "Posted: June 1, 2025",

        load_more_button: "Load More",
        no_more_announcements: "No More Announcements",

        // Announcements Page Specific - Dynamic Content (keys for content loaded by loadMore)
        dynamic_announcement1_title: "Mental Health Support Now Available",
        dynamic_announcement1_description: "We now offer free mental health consultations every Friday. Book your session through the app or visit our main clinic.",
        dynamic_announcement1_date_prefix: "Posted: ", // Prefix for the date string

        dynamic_announcement2_title: "Online Appointment Booking",
        dynamic_announcement2_description: "Skip the line! You can now book your health center appointments online. Try it today in the Services section.",
        dynamic_announcement2_date_prefix: "Posted: ", // Prefix for the date string
      },
      tagalog: {
        // Navigation (common across pages)
        nav_home: "Tahanan",
        nav_about_us: "Tungkol Sa Amin",
        nav_services: "Mga Serbisyo",
        nav_heat_map: "Mapa ng Init",
        nav_blog: "Blog",

        announcements_page_title: "Mga Anunsyo | HealthCardGo",

        // Announcements Page Specific - Static Content
        announcement1_title: "Libreng Health Seminar Ngayong Weekend",
        announcement1_description: "Sumali sa aming libreng health seminar sa Hunyo 8, 2025, sa City Hall Auditorium. Ang mga paksa ay kinabibilangan ng nutrisyon, kalinisan, at pag-iwas sa sakit. Lahat ay malugod na tinatanggap!",
        announcement1_date: "Ipinaskil: Hunyo 4, 2025",

        announcement2_title: "Pinalawig ang Vaccination Drive",
        announcement2_description: "Dahil sa mataas na demand, ang aming libreng vaccination drive ay pinalawig hanggang Hunyo 15, 2025. Bisitahin ang pinakamalapit na health center para sa inyong bakuna!",
        announcement2_date: "Ipinaskil: Hunyo 3, 2025",

        announcement3_title: "Mga Bagong Feature sa HealthCardGo",
        announcement3_description: "Naglunsad kami ng mga bagong feature kabilang ang appointment booking, digital health records, at isang health heat map. Tuklasin ang mga ito ngayon sa inyong dashboard!",
        announcement3_date: "Ipinaskil: Hunyo 1, 2025",

        load_more_button: "Mag-load Pa",
        no_more_announcements: "Wala Nang Anunsyo",

        // Announcements Page Specific - Dynamic Content (keys for content loaded by loadMore)
        dynamic_announcement1_title: "Suporta sa Kalusugang Pangkaisipan Magagamit na Ngayon",
        dynamic_announcement1_description: "Nag-aalok na kami ng libreng konsultasyon sa kalusugang pangkaisipan tuwing Biyernes. Mag-book ng inyong sesyon sa pamamagitan ng app o bisitahin ang aming pangunahing klinika.",
        dynamic_announcement1_date_prefix: "Ipinaskil: ",

        dynamic_announcement2_title: "Online Appointment Booking",
        dynamic_announcement2_description: "Laktawan ang pila! Maaari niyo nang i-book ang inyong mga appointment sa health center online. Subukan ito ngayon sa seksyon ng Mga Serbisyo.",
        dynamic_announcement2_date_prefix: "Ipinaskil: ",
      },
      bisaya: {
        // Navigation (common across pages)
        nav_home: "Balay",
        nav_about_us: "Bahin Kanato",
        nav_services: "Mga Serbisyo",
        nav_heat_map: "Mapa sa Kainit",
        nav_blog: "Blog",
        announcements_page_title: "Mga Pahibalo | HealthCardGo",

        // Announcements Page Specific - Static Content
        announcement1_title: "Libreng Health Seminar Karong Weekend",
        announcement1_description: "Apil sa among libreng health seminar sa Hunyo 8, 2025, sa City Hall Auditorium. Apil sa mga topiko ang nutrisyon, kalimpyo, ug pagpugong sa sakit. Ang tanan welcome!",
        announcement1_date: "Gipatik: Hunyo 4, 2025",

        announcement2_title: "Gipalugway ang Vaccination Drive",
        announcement2_description: "Tungod sa taas nga panginahanglan, gipalugway ang among libreng vaccination drive hangtod Hunyo 15, 2025. Bisitaha ang inyong pinakaduol nga health center para sa inyong bakuna!",
        announcement2_date: "Gipatik: Hunyo 3, 2025",

        announcement3_title: "Bag-ong Features sa HealthCardGo",
        announcement3_description: "Naglunsad kami og bag-ong features lakip na ang appointment booking, digital health records, ug usa ka health heat map. Susiha kini karon sa inyong dashboard!",
        announcement3_date: "Gipatik: Hunyo 1, 2025",

        load_more_button: "Pag-load Pa",
        no_more_announcements: "Wala Nay Pahibalo",

        // Announcements Page Specific - Dynamic Content (keys for content loaded by loadMore)
        dynamic_announcement1_title: "Suporta sa Panglawas sa Pangisip Anaa Na Karon",
        dynamic_announcement1_description: "Nagtanyag na kami karon og libreng konsultasyon sa panglawas sa pangisip matag Biyernes. Pag-book sa inyong sesyon pinaagi sa app o bisitaha ang among main clinic.",
        dynamic_announcement1_date_prefix: "Gipatik: ",

        dynamic_announcement2_title: "Online Appointment Booking",
        dynamic_announcement2_description: "Laktawan ang pila! Mahimo na nimo nga i-book ang inyong mga appointment sa health center online. Sulayi kini karon sa seksyon sa Mga Serbisyo.",
        dynamic_announcement2_date_prefix: "Gipatik: ",
      }
    };

    // Get elements to update current language display
    const currentLangButton = document.getElementById('current-lang-btn');
    const currentLangText = document.getElementById('current-lang-text');

    // Variable to keep track of the current language
    let currentLanguage = localStorage.getItem('selectedLanguage') || 'english'; // Default to English

    // Function to set the language
    function setLanguage(lang) {
      currentLanguage = lang; // Update the current language variable

      // Update the lang attribute of the HTML tag
      document.documentElement.lang = lang;

      // Update all elements with data-key attributes
      document.querySelectorAll('[data-key]').forEach(element => {
        const key = element.getAttribute('data-key');
        if (translations[lang] && translations[lang][key]) {
          // Special handling for the <title> tag
          if (element.tagName === 'TITLE') {
            document.title = translations[lang][key];
          } else {
            element.textContent = translations[lang][key];
          }
        }
      });

      // Update the current language display in the dropdown button
      currentLangText.textContent = lang.charAt(0).toUpperCase() + lang.slice(1); // Capitalize first letter
      // Update flag image
      if (lang === 'english') {
        currentLangButton.querySelector('img').src = 'assets/united.png';
      } else if (lang === 'tagalog' || lang === 'bisaya') {
        currentLangButton.querySelector('img').src = 'assets/flag.png';
      }

      // Store the selected language in localStorage
      localStorage.setItem('selectedLanguage', lang);
    }

    // Add event listeners to language buttons
    document.querySelectorAll('.lang-button').forEach(button => {
      button.addEventListener('click', (event) => {
        const lang = event.currentTarget.getAttribute('data-lang');
        setLanguage(lang);
      });
    });

    // Load preferred language from localStorage on page load
    document.addEventListener('DOMContentLoaded', () => {
      const storedLang = localStorage.getItem('selectedLanguage');
      if (storedLang) {
        // setLanguage(storedLang);
      } else {
        // Default to English if no language is stored
        // setLanguage('english');
      }
    });

    // Modified loadMore function to handle translations for new content
    function loadMore() {
      const newsfeed = document.querySelector('.newsfeed');
      const newAnnouncementsData = [
        {
          icon: 'ri-user-heart-line',
          titleKey: 'dynamic_announcement1_title', // Use keys for titles/descriptions
          descriptionKey: 'dynamic_announcement1_description',
          date: 'May 30, 2025',
          datetime: '2025-05-30',
          datePrefixKey: 'dynamic_announcement1_date_prefix'
        },
        {
          icon: 'ri-calendar-check-line',
          titleKey: 'dynamic_announcement2_title',
          descriptionKey: 'dynamic_announcement2_description',
          date: 'May 28, 2025',
          datetime: '2025-05-28',
          datePrefixKey: 'dynamic_announcement2_date_prefix'
        }
      ];

      newAnnouncementsData.forEach(item => {
        const article = document.createElement('article');
        article.className = 'newsfeed-card';

        // Get translated content based on the currentLanguage
        const title = translations[currentLanguage][item.titleKey];
        const description = translations[currentLanguage][item.descriptionKey];
        const datePrefix = translations[currentLanguage][item.datePrefixKey];

        article.innerHTML = `
          <i class="${item.icon} newsfeed-icon"></i>
          <div class="newsfeed-content">
            <h3 class="newsfeed-title" data-key="${item.titleKey}">${title}</h3>
            <p class="newsfeed-description" data-key="${item.descriptionKey}">${description}</p>
            <time class="newsfeed-date" data-key="${item.datePrefixKey}" datetime="${item.datetime}">${datePrefix}${item.date}</time>
          </div>
        `;
        newsfeed.insertBefore(article, newsfeed.querySelector('.load-more-btn'));
      });

      const btn = document.querySelector('.load-more-btn');
      btn.disabled = true;
      // Update the "No More Announcements" text using the translation object
      btn.textContent = translations[currentLanguage]['no_more_announcements'];
    }
  </script>
@endpush
