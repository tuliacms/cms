<footer>
    {#<div class="cms-status">
        <a href="https://tuliacms.org/intl/help" target="_blank" title="Need help?" rel="noopener">
            <i class="status-icon fas fa-question-circle"></i>
            <span class="status-text">Need help?</span>
        </a>
    </div>#}
    <div class="cms-version"><a href="http://tuliacms.org/" target="_blank" title="Tulia CMS" rel="noopener">Tulia CMS v. {{ constant('Tulia\\Cms\\Platform\\Version::VERSION') }}</a></div>
</footer>

{% include '@backend/search-anything/search-anything.tpl' %}

<div class="modal fade" id="tuliacms-found-bug-modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ 'didYouFoundBug'|trans }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe data-src="https://docs.google.com/forms/d/e/1FAIpQLSdxaicfnJMJNa4V_kbfqL6Zcuuz9syW5r4EGA4g8t0nx2-IJg/viewform?embedded=true" width="750" height="850" frameborder="0" marginheight="0" marginwidth="0">Ładuję…</iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ 'close'|trans }}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tuliacms-intro-modal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Witaj w Tulia CMS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-5">
                <h1 class="display-5 fw-bold mb-5">Witaj w Tulia CMS</h1>
                <div class="col-lg-8 mx-auto">
                    <p class="lead mb-4">Rozpoczynasz właśnie testy systemu. Miej na uwadze to, że system może zawierać błędy - ale od czego są testy prawda <i class="far fa-smile-wink"></i></p>
                    <p class="lead mb-4">Będzie nam miło, gdy zgłosisz znalezione błędy za pomocą formularza dostępnego pod przyciskiem na dole po lewej: <a href="#" title="Znalazłeś błąd?" class="d-inline-block"><i class="fas fa-bug"></i> Znalazłeś błąd?</a></p>
                    <p class="lead mb-0">Miłego testowania <i class="far fa-smile-wink"></i></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ 'close'|trans }}</button>
            </div>
        </div>
    </div>
</div>

{% assets ['js_cookie'] %}

<script nonce="{{ csp_nonce() }}">
    $(function () {
        const modal = document.getElementById('tuliacms-found-bug-modal');
        modal.addEventListener('show.bs.modal', event => {
            const iframe = modal.querySelector('iframe');
            const src = iframe.getAttribute('data-src');
            iframe.setAttribute('src', src);
        });

        if (Cookies.get('tulia-intromodal-showed') !== 'yes') {
            const intromodal = document.getElementById('tuliacms-intro-modal');
            (new bootstrap.Modal(intromodal)).show();
            intromodal.addEventListener('hidden.bs.modal', event => {
                Cookies.set('tulia-intromodal-showed', 'yes');
            });
        }
    });
</script>
