@extends('layouts.app')

@section('content')
    <div class="container">
        <h4 class="mb-4"><i class="bi bi-gear text-primary"></i> System Settings</h4>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5>AI Configuration</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.update') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="ai_api_key" class="form-label">AI API Key</label>
                        <input type="password" class="form-control" id="ai_api_key" name="ai_api_key"
                            value="{{ $settings['ai_api_key'] ? '****' : '' }}" placeholder="Enter your API key">
                        <small class="text-muted">Leave empty to keep existing key</small>
                    </div>

                    <div class="mb-3">
                        <label for="ai_model" class="form-label">AI Model</label>
                        <select class="form-select" id="ai_model" name="ai_model">
                            <option value="gpt-4" {{ $settings['ai_model'] == 'gpt-4' ? 'selected' : '' }}>GPT-4</option>
                            <option value="gpt-4-turbo" {{ $settings['ai_model'] == 'gpt-4-turbo' ? 'selected' : '' }}>GPT-4
                                Turbo</option>
                            <option value="gpt-3.5-turbo" {{ $settings['ai_model'] == 'gpt-3.5-turbo' ? 'selected' : '' }}>
                                GPT-3.5 Turbo</option>
                            <option value="claude-3-opus" {{ $settings['ai_model'] == 'claude-3-opus' ? 'selected' : '' }}>
                                Claude 3 Opus</option>
                            <option value="claude-3-sonnet"
                                {{ $settings['ai_model'] == 'claude-3-sonnet' ? 'selected' : '' }}>Claude 3 Sonnet</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="ai_endpoint" class="form-label">API Endpoint</label>
                        <input type="url" class="form-control" id="ai_endpoint" name="ai_endpoint"
                            value="{{ $settings['ai_endpoint'] }}" placeholder="https://api.openai.com/v1">
                        <small class="text-muted">Leave at default for OpenAI. Use custom endpoint for Azure, Anthropic,
                            etc.</small>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5>Company Configuration</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.update') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="company_name" class="form-label">Company Name</label>
                        <input type="text" class="form-control" id="company_name" name="company_name"
                            value="{{ $settings['company_name'] }}" placeholder="Enter company name">
                    </div>

                    <div class="mb-3">
                        <label for="company_currency" class="form-label">Default Currency</label>
                        <select class="form-select" id="company_currency" name="company_currency">
                            <option value="USD" {{ $settings['company_currency'] == 'USD' ? 'selected' : '' }}>USD - US
                                Dollar</option>
                            <option value="EUR" {{ $settings['company_currency'] == 'EUR' ? 'selected' : '' }}>EUR -
                                Euro</option>
                            <option value="GBP" {{ $settings['company_currency'] == 'GBP' ? 'selected' : '' }}>GBP -
                                British Pound</option>
                            <option value="JPY" {{ $settings['company_currency'] == 'JPY' ? 'selected' : '' }}>JPY -
                                Japanese Yen</option>
                            <option value="CAD" {{ $settings['company_currency'] == 'CAD' ? 'selected' : '' }}>CAD -
                                Canadian Dollar</option>
                            <option value="AUD" {{ $settings['company_currency'] == 'AUD' ? 'selected' : '' }}>AUD -
                                Australian Dollar</option>
                            <option value="CHF" {{ $settings['company_currency'] == 'CHF' ? 'selected' : '' }}>CHF -
                                Swiss Franc</option>
                            <option value="CNY" {{ $settings['company_currency'] == 'CNY' ? 'selected' : '' }}>CNY -
                                Chinese Yuan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="company_date_format" class="form-label">Date Format</label>
                        <select class="form-select" id="company_date_format" name="company_date_format">
                            <option value="Y-m-d" {{ $settings['company_date_format'] == 'Y-m-d' ? 'selected' : '' }}>
                                2024-12-31</option>
                            <option value="d/m/Y" {{ $settings['company_date_format'] == 'd/m/Y' ? 'selected' : '' }}>
                                31/12/2024</option>
                            <option value="m/d/Y" {{ $settings['company_date_format'] == 'm/d/Y' ? 'selected' : '' }}>
                                12/31/2024</option>
                            <option value="d-M-Y" {{ $settings['company_date_format'] == 'd-M-Y' ? 'selected' : '' }}>
                                31-Dec-2024</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="company_fiscal_year_start" class="form-label">Fiscal Year Start Month</label>
                        <select class="form-select" id="company_fiscal_year_start" name="company_fiscal_year_start">
                            @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                                <option value="{{ $month }}"
                                    {{ $settings['company_fiscal_year_start'] == $month ? 'selected' : '' }}>
                                    {{ $month }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Company Settings</button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5>Current Configuration</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><strong>API Key:</strong> {{ $settings['ai_api_key'] ? '••••••••' : 'Not set' }}</li>
                    <li><strong>Model:</strong> {{ $settings['ai_model'] }}</li>
                    <li><strong>Endpoint:</strong> {{ $settings['ai_endpoint'] }}</li>
                    <li><strong>Company:</strong> {{ $settings['company_name'] ?: 'Not set' }}</li>
                    <li><strong>Currency:</strong> {{ $settings['company_currency'] ?: 'GBP' }}</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
