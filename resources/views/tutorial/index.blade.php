@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4"><i class="bi bi-book"></i> Tutorial</h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">How to Use the FMS</h5>
                    
                    <div class="mt-4">
                        <h6>1. Dashboard</h6>
                        <p>The dashboard provides an overview of your financial status. It shows key metrics and summaries at a glance.</p>
                        
                        <h6 class="mt-3">2. Cost Centres</h6>
                        <p>Cost Centres are used to organize and track expenses by department or project. Only admins can manage cost centres.</p>
                        
                        <h6 class="mt-3">3. Accounts</h6>
                        <p>Accounts represent different expense categories (e.g., Travel, Supplies, Salaries). Finance users and admins can manage accounts.</p>
                        
                        <h6 class="mt-3">4. Budgets</h6>
                        <p>Create and manage budgets for each cost centre. Budgets are organized by year and month using budget lines.</p>
                        
                        <h6 class="mt-3">5. Actuals</h6>
                        <p>Record actual expenses against your budget line items. You can add details to actuals for better tracking.</p>
                        
                        <h6 class="mt-3">6. Import</h6>
                        <p>Import actuals from CSV files. Download the template to ensure proper data formatting.</p>
                        
                        <h6 class="mt-3">7. Reports</h6>
                        <p>Generate financial reports showing budget vs actual comparison.</p>
                        
                        <h6 class="mt-3">8. Forecast</h6>
                        <p>View financial forecasts based on current spending patterns.</p>
                        
                        <h6 class="mt-3">9. Alerts</h6>
                        <p>Set up alerts to be notified when spending exceeds budget thresholds.</p>
                        
                        <h6 class="mt-3">10. AI Analysis</h6>
                        <p>Use AI-powered analysis to gain insights into your spending patterns.</p>
                        
                        <h6 class="mt-3">11. Charts</h6>
                        <p>Visualize your financial data with various charts including monthly trends, account distribution, and variance analysis.</p>
                        
                        <h6 class="mt-3">12. Settings</h6>
                        <p>Manage your profile and change your password. Admins can access additional system settings.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection