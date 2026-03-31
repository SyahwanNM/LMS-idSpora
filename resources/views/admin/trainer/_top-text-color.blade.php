<style>
    /* Only change font color in top sections of admin trainer pages to white.
       Targets typical textual elements without altering backgrounds or buttons. */
    .trainer-hero, .send-hero, .queue-hero { }

    .trainer-hero h1, .trainer-hero h2, .trainer-hero h3, .trainer-hero p, .trainer-hero span,
    .trainer-hero a, .trainer-hero small, .trainer-hero .badge, .trainer-hero .fw-800,
    .send-hero h1, .send-hero h2, .send-hero h3, .send-hero p, .send-hero span,
    .send-hero a, .send-hero small, .send-hero .badge, .send-hero .fw-800,
    .queue-hero h1, .queue-hero h2, .queue-hero h3, .queue-hero p, .queue-hero span,
    .queue-hero a, .queue-hero small, .queue-hero .badge, .queue-hero .fw-800 {
        color: #ffffff !important;
    }

    /* Make link icons and inline labels white as well */
    .trainer-hero i, .send-hero i, .queue-hero i { color: #ffffff !important; }

    /* Keep interactive controls' backgrounds intact; only change their text color if present inside the hero */
    .trainer-hero .btn, .send-hero .btn, .queue-hero .btn { color: inherit !important; }
</style>
