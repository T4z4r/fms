@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Settings</h1>

    @if(session('success'))
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
                           value="{{ $settings['ai_api_key'] ? '****' : '' }}" 
                           placeholder="Enter your API key">
                    <small class="text-muted">Leave empty to keep existing key</small>
                </div>

                <div class="mb-3">
                    <label for="ai_model" class="form-label">AI Model</label>
                    <select class="form-select" id="ai_model" name="ai_model">
                        <option value="gpt-4" {{ $settings['ai_model'] == 'gpt-4' ? 'selected' : '' }}>GPT-4</option>
                        <option value="gpt-4-turbo" {{ $settings['ai_model'] == 'gpt-4-turbo' ? 'selected' : '' }}>GPT-4 Turbo</option>
                        <option value="gpt-3.5-turbo" {{ $settings['ai_model'] == 'gpt-3.5-turbo' ? 'selected' : '' }}>GPT-3.5 Turbo</option>
                        <option value="claude-3-opus" {{ $settings['ai_model'] == 'claude-3-opus' ? 'selected' : '' }}>Claude 3 Opus</option>
                        <option value="claude-3-sonnet" {{ $settings['ai_model'] == 'claude-3-sonnet' ? 'selected' : '' }}>Claude 3 Sonnet</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="ai_endpoint" class="form-label">API Endpoint</label>
                    <input type="url" class="form-control" id="ai_endpoint" name="ai_endpoint" 
                           value="{{ $settings['ai_endpoint'] }}" 
                           placeholder="https://api.openai.com/v1">
                    <small class="text-muted">Leave at default for OpenAI. Use custom endpoint for Azure, Anthropic, etc.</small>
                </div>

                <button type="submit" class="btn btn-primary">Save Settings</button>
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
            </ul>
        </div>
    </div>
</div>
@endsection
