<div id="main">
  <!-- Announcement Banner -->
  @if($this->latestAnnouncement)
    <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 p-4 mb-6">
      <div class="flex items-start">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
          </svg>
        </div>
        <div class="ml-3 flex-1">
          <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
            {{ $this->latestAnnouncement->title }}
          </h3>
          <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
            <p>{{ $this->latestAnnouncement->content }}</p>
          </div>
        </div>
      </div>
    </div>
  @endif

  <x-homepage-header id="home">
    <div class="header__content">
      <h1 data-key="header_title">{{ __('app.header_title') }}</h1>
      <p data-key="header_description">
        {{ __('app.header_description') }}
      </p>
      <a href="{{ route('patient.book-appointment') }}" class="btn w-fit">{{ __('app.book_appointment') }}</a>
    </div>
    <div class="modal" id="confidentialityModal">
      <div class="modal-content">
        <span class="close" id="closeConfidentiality">&times;</span>
        <h3 data-key="conf_notice_title">{{ __('app.conf_notice_title') }}</h3>
        <p data-key="conf_notice_text">{{ __('app.conf_notice_text') }}</p>
        <button id="toLoginBtn" class="btn" onclick="window.location.href='login.php'"
          data-key="btn_proceed_login">Proceed to Login</button>
      </div>
    </div>
  </x-homepage-header>

  <section class="section__container service__container" id="service">
    <div class="service__header">
      <div class="service__header__content">
        <h2 class="section__header" data-key="service_header">{{ __('app.service_header') }}</h2>
        <p data-key="service_description">
          {{ __('app.service_description') }}
        </p>
      </div>
    </div>
    <div class="service__grid">
      <div class="service__card">
        <span><i class="ri-microscope-line"></i></span>
        <h4 data-key="card_healthcard_title">{{ __('app.card_healthcard_title') }}</h4>
        <p data-key="card_healthcard_description">
          {{ __('app.card_healthcard_description') }}
        </p>

        <flux:modal.trigger name="healthcard-modal">
            <a href="javascript:void(0);" data-key="btn_learn_more">{{ __('app.learn_more') }}</a>
        </flux:modal.trigger>
      </div>
      <div class="service__card">
        <span><i class="ri-mental-health-line"></i></span>
        <h4 data-key="card_hiv_title">{{ __('app.card_hiv_title') }}</h4>
        <p data-key="card_hiv_description">
          {{ __('app.card_hiv_description') }}
        </p>

        <flux:modal.trigger name="hiv-modal">
            <a href="javascript:void(0);" data-key="btn_learn_more">{{ __('app.learn_more') }}</a>
        </flux:modal.trigger>
      </div>
      <div class="service__card">
        <span><i class="ri-hospital-line"></i></span>
        <h4 data-key="card_pregnancy_title">{{ __('app.card_pregnancy_title') }}</h4>
        <p data-key="card_pregnancy_description">
            {{ __('app.card_pregnancy_description') }}
        </p>

        <flux:modal.trigger name="pregnancy-modal">
            <a href="javascript:void(0);" data-key="btn_learn_more">{{ __('app.learn_more') }}</a>
        </flux:modal.trigger>
      </div>
    </div>
  </section>

  <section class="section__container about__container" id="about">
    <div class="about__content">
      <h2 class="section__header" data-key="about_us_header">{{ __('app.about_us_header') }}</h2>
      <p data-key="about_us_p1">
        {{ __('app.about_us_1') }}
      </p>
      <p data-key="about_us_p2">
        {{ __('app.about_us_2') }}
      </p>
      <p data-key="about_us_p3">
        {{ __('app.about_us_3') }}
      </p>
    </div>
    <div class="about__image">
      <img src="{{ asset('assets/images/A2.jpg') }}" alt="about" />
    </div>
  </section>

  <section class="section__container why__container" id="blog">
    <div class="why__image">
      <img src="{{ asset('assets/images/A3.jpg') }}" alt="why choose us" />
    </div>
    <div class="why__content">
      <h2 class="section__header" data-key="why_choose_us_header">{{ __('app.why_choose_us_header') }}</h2>
      <p data-key="why_choose_us_description">
        {{ __('app.why_choose_us_description') }}
      </p>
      <div class="why__grid">
        <span><i class="ri-hand-heart-line"></i></span>
        <div>
          <h4 data-key="intensive_care_title">{{ __('app.intensive_care_title') }}</h4>
          <p data-key="intensive_care_description">
            {{ __('app.intensive_care_description') }}
          </p>
        </div>
        <span><i class="ri-truck-line"></i></span>
        <div>
          <h4 data-key="free_ambulance_title">{{ __('app.free_ambulance_title') }}</h4>
          <p data-key="free_ambulance_description">
            {{ __('app.free_ambulance_description') }}
          </p>
        </div>
        <span><i class="ri-hospital-line"></i></span>
        <div>
          <h4 data-key="med_surg_title">{{ __('app.med_surg_title') }}</h4>
          <p data-key="med_surg_description">
            {{ __('app.med_surg_description') }}
          </p>
        </div>
      </div>
    </div>
  </section>

  <section class="section__container doctors__container" id="pages">
    <div class="doctors__header">
      <div class="doctors__header__content w-full">
        <h2 class="section__header" data-key="heatmap_header">{{ __('app.heatmap_header') }}</h2>
        <p data-key="heatmap_description">
          {{ __('app.heatmap_description') }}
        </p>
        <div class="w-full mt-6!">
          <div id="disease-heatmap" class="map"></div>
        </div>
      </div>
    </div>
  </section>

  <footer class="footer">
    <div class="section__container footer__container">
      <div class="footer__col">
        <h3>Health<span>Care</span></h3>
        <p data-key="footer_p1">
          {{ __('app.footer_p1') }}
        </p>
        <p data-key="footer_p2">
          {{ __('app.footer_p2') }}
        </p>
      </div>
      <div class="footer__col">
        <h4 data-key="footer_about_us_title">{{ __('app.footer_about_us_title') }}</h4>
        <p data-key="nav_home">{{ __('app.nav_home') }}</p>
        <p data-key="nav_about_us">{{ __('app.nav_about') }}</p>
        <p data-key="footer_work_with_us">{{ __('app.footer_work_with_us') }}</p>
        <p data-key="footer_our_blog">{{ __('app.footer_our_blog') }}</p>
        <p data-key="announcement.php">{{ __('app.nav_announcements') }}</p>
        <p data-key="footer_terms_conditions">{{ __('app.footer_terms_conditions') }}</p>
      </div>
      <div class="footer__col">
        <h4 data-key="footer_services_title">{{ __('app.footer_services_title') }}</h4>
        <p data-key="footer_services_title">{{ __('app.footer_services_title') }}</p>
        <p data-key="preg.php">{{ __('app.footer_pregnancy_checkup') }}</p>
        <p data-key="hiv.php">{{ __('app.footer_hiv') }}</p>
        <p data-key="footer_privacy_policy">{{ __('app.footer_privacy_policy') }}</p>
        <p data-key="footer_our_stores">{{ __('app.footer_our_stores') }}</p>
      </div>
      <div class="footer__col">
        <h4 data-key="footer_contact_us_title">{{ __('app.footer_contact_us_title') }}</h4>
        <p data-key="footer_address">
          <i class="ri-map-pin-2-fill"></i> Quezon St. City Health Office Compound Brgy. New Pandan 8105 Panabo,
          Philippines
        </p>
        <p data-key="footer_email"><i class="ri-mail-fill"></i> panabocityhealth@gmail.com</p>
        <!-- <p data-key="footer_phone"><i class="ri-phone-fill"></i> (+012) 3456 789</p> -->
      </div>
    </div>
    <div class="footer__bar">
      <div class="footer__bar__content">
        <p>ConPactor. Copyright &copy; @php echo date('Y'); @endphp</p>
        <div class="footer__socials">
          <span><i class="ri-instagram-line"></i></span>
          <span><i class="ri-facebook-fill"></i></span>
          <span><i class="ri-heart-fill"></i></span>
          <span><i class="ri-twitter-fill"></i></span>
        </div>
      </div>
    </div>
  </footer>

  @include('partials.modals.card-healthcard-modal')
  @include('partials.modals.card-hiv-modal')
  @include('partials.modals.card-pregnancy-modal')
</div>

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize heatmap data
      window.surveillanceData = {
        heatmapData: @js($this->heatmapData)
      };

      // Initialize the heatmap
      initializeHomeHeatmap();
    });

    // Listen for Livewire navigation (SPA-style navigation)
    document.addEventListener('livewire:navigated', () => {
      console.log('Livewire navigated, reinitializing...');
      window.surveillanceData = {
        heatmapData: @js($this->heatmapData)
      };
      initializeHomeHeatmap();
    });

    function initializeHomeHeatmap() {
      const mapContainer = document.getElementById('disease-heatmap');
      if (!mapContainer) return;

      // Initialize Leaflet map centered on Panabo City
      const map = L.map('disease-heatmap').setView([7.5119, 125.6838], 12);

      // Add OpenStreetMap tiles
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(map);

      // Get heatmap data
      const heatmapData = window.surveillanceData?.heatmapData || [];

      // Add markers for each barangay with cases
      heatmapData.forEach(item => {
        if (item.latitude && item.longitude && item.cases_count > 0) {
          // Define color based on case count
          let color = '#10b981'; // green for low cases
          if (item.cases_count > 20) color = '#ef4444'; // red for high cases
          else if (item.cases_count > 10) color = '#f59e0b'; // orange for medium cases
          else if (item.cases_count > 5) color = '#eab308'; // yellow for moderate cases

          // Create circle marker
          const circle = L.circleMarker([item.latitude, item.longitude], {
            color: color,
            fillColor: color,
            fillOpacity: 0.6,
            radius: Math.min(item.cases_count * 3, 30) // Scale radius based on cases
          }).addTo(map);

          // Add popup with barangay info
          circle.bindPopup(`
            <div style="min-width: 150px;">
              <strong>${item.barangay_name}</strong><br>
              Cases: ${item.cases_count}
            </div>
          `);
        }
      });

      // Add legend
      const legend = L.control({
        position: 'bottomright'
      });
      legend.onAdd = function(map) {
        const div = L.DomUtil.create('div', 'info legend');
        div.style.backgroundColor = 'white';
        div.style.padding = '10px';
        div.style.borderRadius = '5px';
        div.style.boxShadow = '0 2px 4px rgba(0,0,0,0.2)';

        div.innerHTML = `
          <strong>Cases</strong><br>
          <i style="background: #10b981; width: 18px; height: 18px; display: inline-block; border-radius: 50%;"></i> 1-5<br>
          <i style="background: #eab308; width: 18px; height: 18px; display: inline-block; border-radius: 50%;"></i> 6-10<br>
          <i style="background: #f59e0b; width: 18px; height: 18px; display: inline-block; border-radius: 50%;"></i> 11-20<br>
          <i style="background: #ef4444; width: 18px; height: 18px; display: inline-block; border-radius: 50%;"></i> 20+
        `;
        return div;
      };
      legend.addTo(map);
    }
  </script>
@endpush
