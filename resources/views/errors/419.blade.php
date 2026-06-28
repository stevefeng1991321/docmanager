<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('errors.419_title') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background-color: #161b27;
            color: #fff;
            font-family: ui-sans-serif, system-ui, -apple-system, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            min-height: 100vh;
            gap: 28px;
        }
        .row {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .code {
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: .15em;
            text-transform: uppercase;
        }
        .divider {
            width: 1px;
            height: 1.25rem;
            background: rgba(255,255,255,0.35);
        }
        .message {
            font-size: .85rem;
            letter-spacing: .15em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.85);
        }
        .hint {
            font-size: .78rem;
            color: rgba(255,255,255,0.45);
            letter-spacing: .05em;
        }
        .back {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 18px;
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 6px;
            background: transparent;
            color: rgba(255,255,255,0.7);
            font-size: .78rem;
            letter-spacing: .1em;
            text-transform: uppercase;
            cursor: pointer;
            text-decoration: none;
            transition: border-color .15s, color .15s;
        }
        .back:hover { border-color: rgba(255,255,255,0.45); color: #fff; }
        .back svg { flex-shrink: 0; }
    </style>
</head>
<body>
    <div class="row">
        <span class="code">419</span>
        <div class="divider"></div>
        <span class="message">{{ __('errors.419_heading') }}</span>
    </div>

    <p class="hint">{{ __('errors.419_message') }}</p>

    <a href="javascript:history.back()" class="back">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
        </svg>
        {{ __('errors.go_back') }}
    </a>
</body>
</html>
