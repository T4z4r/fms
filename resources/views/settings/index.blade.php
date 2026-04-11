@extends('layouts.app')

@section('content')
    <style>
        .settings-shell {
            margin-top: 1.5rem;
        }

        .settings-card-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
        }

        .settings-card-title {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
        }

        .settings-card-title i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .settings-card-copy {
            font-size: 0.9rem;
            margin: 0.35rem 0 0;
        }

        .settings-section-gap + .settings-section-gap {
            margin-top: 1.5rem;
        }

        .settings-field {
            padding: 1rem;
            border: 1px solid var(--bs-border-color);
        }

        .settings-field .form-label {
            font-weight: 600;
            margin-bottom: 0.45rem;
        }

        .settings-submit {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding-inline: 1rem;
            font-weight: 600;
        }

        .settings-summary {
            position: sticky;
            top: 1.25rem;
        }

        .settings-summary-list {
            display: grid;
            gap: 0.85rem;
        }

        .settings-summary-item {
            padding: 1rem;
            border: 1px solid var(--bs-border-color);
        }

        .settings-summary-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.35rem;
        }

        .settings-summary-value {
            margin: 0;
            font-weight: 600;
            word-break: break-word;
        }

        @media (max-width: 991.98px) {
            .settings-summary {
                position: static;
            }
        }

        @media (max-width: 767.98px) {
            .settings-card-header {
                flex-direction: column;
            }

            .settings-field,
            .settings-summary-item {
                padding: 0.85rem;
            }

            .settings-submit {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div class="container py-2 py-lg-3 page-shell">
        <div class="mb-4">
            <div class="text-primary small fw-semibold text-uppercase mb-2">
                <i class="bi bi-sliders me-1"></i> Administration
            </div>
            <h2 class="fw-bold mb-2">System Settings</h2>
            <p class="text-muted mb-0">
                Manage your AI connection, organization defaults, and core platform preferences from one place.
            </p>
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm mt-4 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="row g-4 settings-shell">
            <div class="col-lg-8">
                <div class="settings-section-gap">
                    <div class="card">
                        <div class="card-header settings-card-header">
                            <div>
                                <h5 class="settings-card-title">
                                    <i class="bi bi-cpu text-primary"></i>
                                    <span>AI Configuration</span>
                                </h5>
                                <p class="settings-card-copy text-muted">Set the model, endpoint, and authentication used for AI-powered features.</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('settings.update') }}">
                                @csrf

                                <div class="settings-section-gap">
                                    <div class="settings-field">
                                        <label for="ai_api_key" class="form-label">AI API Key</label>
                                        <input type="password" class="form-control" id="ai_api_key" name="ai_api_key"
                                            value="{{ $settings['ai_api_key'] ? '****' : '' }}" placeholder="Enter your API key">
                                        <div class="form-text">Leave this blank if you want to keep the existing key.</div>
                                    </div>

                                    <div class="settings-field">
                                        <label for="ai_model" class="form-label">AI Model</label>
                                        <select class="form-select" id="ai_model" name="ai_model">
                                            <option value="gpt-4" {{ $settings['ai_model'] == 'gpt-4' ? 'selected' : '' }}>GPT-4</option>
                                            <option value="gpt-4-turbo" {{ $settings['ai_model'] == 'gpt-4-turbo' ? 'selected' : '' }}>GPT-4 Turbo</option>
                                            <option value="gpt-3.5-turbo" {{ $settings['ai_model'] == 'gpt-3.5-turbo' ? 'selected' : '' }}>GPT-3.5 Turbo</option>
                                            <option value="claude-3-opus" {{ $settings['ai_model'] == 'claude-3-opus' ? 'selected' : '' }}>Claude 3 Opus</option>
                                            <option value="claude-3-sonnet" {{ $settings['ai_model'] == 'claude-3-sonnet' ? 'selected' : '' }}>Claude 3 Sonnet</option>
                                        </select>
                                    </div>

                                    <div class="settings-field">
                                        <label for="ai_endpoint" class="form-label">API Endpoint</label>
                                        <input type="url" class="form-control" id="ai_endpoint" name="ai_endpoint"
                                            value="{{ $settings['ai_endpoint'] }}" placeholder="https://api.openai.com/v1">
                                        <div class="form-text">
                                            Keep the default for OpenAI, or supply a custom endpoint for Azure, Anthropic, or another provider.
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary settings-submit">
                                        <i class="bi bi-save"></i>
                                        <span>Save AI Settings</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="settings-section-gap">
                    <div class="card">
                        <div class="card-header settings-card-header">
                            <div>
                                <h5 class="settings-card-title">
                                    <i class="bi bi-building text-primary"></i>
                                    <span>Company Configuration</span>
                                </h5>
                                <p class="settings-card-copy text-muted">Define the defaults that shape reporting, currency display, and fiscal planning.</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('settings.update') }}">
                                @csrf

                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="settings-field h-100">
                                            <label for="company_name" class="form-label">Company Name</label>
                                            <input type="text" class="form-control" id="company_name" name="company_name"
                                                value="{{ $settings['company_name'] }}" placeholder="Enter company name">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="settings-field h-100">
                                            <label for="company_currency" class="form-label">Default Currency</label>
                                            <select class="form-select" id="company_currency" name="company_currency">
                                                <option value="USD" {{ $settings['company_currency'] == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                                <option value="EUR" {{ $settings['company_currency'] == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                                <option value="GBP" {{ $settings['company_currency'] == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                                <option value="JPY" {{ $settings['company_currency'] == 'JPY' ? 'selected' : '' }}>JPY - Japanese Yen</option>
                                                <option value="CAD" {{ $settings['company_currency'] == 'CAD' ? 'selected' : '' }}>CAD - Canadian Dollar</option>
                                                <option value="AUD" {{ $settings['company_currency'] == 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
                                                <option value="CHF" {{ $settings['company_currency'] == 'CHF' ? 'selected' : '' }}>CHF - Swiss Franc</option>
                                                <option value="CNY" {{ $settings['company_currency'] == 'CNY' ? 'selected' : '' }}>CNY - Chinese Yuan</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="settings-field h-100">
                                            <label for="company_date_format" class="form-label">Date Format</label>
                                            <select class="form-select" id="company_date_format" name="company_date_format">
                                                <option value="Y-m-d" {{ $settings['company_date_format'] == 'Y-m-d' ? 'selected' : '' }}>2024-12-31</option>
                                                <option value="d/m/Y" {{ $settings['company_date_format'] == 'd/m/Y' ? 'selected' : '' }}>31/12/2024</option>
                                                <option value="m/d/Y" {{ $settings['company_date_format'] == 'm/d/Y' ? 'selected' : '' }}>12/31/2024</option>
                                                <option value="d-M-Y" {{ $settings['company_date_format'] == 'd-M-Y' ? 'selected' : '' }}>31-Dec-2024</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="settings-field h-100">
                                            <label for="company_fiscal_year_start" class="form-label">Fiscal Year Start Month</label>
                                            <select class="form-select" id="company_fiscal_year_start" name="company_fiscal_year_start">
                                                @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                                    <option value="{{ $month }}"
                                                        {{ $settings['company_fiscal_year_start'] == $month ? 'selected' : '' }}>
                                                        {{ $month }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary settings-submit">
                                        <i class="bi bi-save"></i>
                                        <span>Save Company Settings</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="settings-summary">
                    <div class="card">
                        <div class="card-header settings-card-header">
                            <div>
                                <h5 class="settings-card-title">
                                    <i class="bi bi-shield-check text-primary"></i>
                                    <span>Current Configuration</span>
                                </h5>
                                <p class="settings-card-copy text-muted">A quick snapshot of the settings currently applied to your workspace.</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="settings-summary-list">
                                <div class="settings-summary-item">
                                    <span class="settings-summary-label">API Key</span>
                                    <p class="settings-summary-value">{{ $settings['ai_api_key'] ? '••••••••' : 'Not set' }}</p>
                                </div>
                                <div class="settings-summary-item">
                                    <span class="settings-summary-label">AI Model</span>
                                    <p class="settings-summary-value">{{ $settings['ai_model'] ?: 'Not set' }}</p>
                                </div>
                                <div class="settings-summary-item">
                                    <span class="settings-summary-label">API Endpoint</span>
                                    <p class="settings-summary-value">{{ $settings['ai_endpoint'] ?: 'Not set' }}</p>
                                </div>
                                <div class="settings-summary-item">
                                    <span class="settings-summary-label">Company</span>
                                    <p class="settings-summary-value">{{ $settings['company_name'] ?: 'Not set' }}</p>
                                </div>
                                <div class="settings-summary-item">
                                    <span class="settings-summary-label">Currency</span>
                                    <p class="settings-summary-value">{{ $settings['company_currency'] ?: 'GBP' }}</p>
                                </div>
                                <div class="settings-summary-item">
                                    <span class="settings-summary-label">Fiscal Year Start</span>
                                    <p class="settings-summary-value">{{ $settings['company_fiscal_year_start'] ?: 'Not set' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
