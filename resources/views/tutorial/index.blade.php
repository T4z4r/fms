@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4"><i class="bi bi-book"></i> Tutorial</h4>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-speedometer2"></i> Getting Started
                </div>
                <div class="card-body">
                    <h6><i class="bi bi-speedometer2 text-primary"></i> 1. Dashboard</h6>
                    <p class="small text-muted">The dashboard provides an overview of your financial status. It shows key metrics and summaries at a glance.</p>
                    
                    <hr>
                    
                    <h6><i class="bi bi-diagram-3 text-primary"></i> 2. Cost Centres</h6>
                    <p class="small text-muted">Cost Centres are used to organize and track expenses by department or project. Only admins can manage cost centres.</p>
                    
                    <hr>
                    
                    <h6><i class="bi bi-wallet2 text-primary"></i> 3. Accounts</h6>
                    <p class="small text-muted">Accounts represent different expense categories (e.g., Travel, Supplies, Salaries). Finance users and admins can manage accounts.</p>
                    
                    <hr>
                    
                    <h6><i class="bi bi-calculator text-primary"></i> 4. Budgets</h6>
                    <p class="small text-muted">Create and manage budgets for each cost centre. Budgets are organized by year and month using budget lines.</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-cash-stack"></i> Recording Transactions
                </div>
                <div class="card-body">
                    <h6><i class="bi bi-receipt text-success"></i> 5. Actuals</h6>
                    <p class="small text-muted">Record actual expenses against your budget line items. You can add details to actuals for better tracking.</p>
                    
                    <hr>
                    
                    <h6><i class="bi bi-upload text-success"></i> 6. Import</h6>
                    <p class="small text-muted">Import actuals from CSV files. Download the template to ensure proper data formatting.</p>
                    
                    <hr>
                    
                    <h6><i class="bi bi-file-earmark-ruled text-success"></i> 7. Reports</h6>
                    <p class="small text-muted">Generate financial reports showing budget vs actual comparison.</p>
                    
                    <hr>
                    
                    <h6><i class="bi bi-graph-up-arrow text-success"></i> 8. Forecast</h6>
                    <p class="small text-muted">View financial forecasts based on current spending patterns.</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-dark">
                    <i class="bi bi-exclamation-triangle"></i> Monitoring
                </div>
                <div class="card-body">
                    <h6><i class="bi bi-bell text-warning"></i> 9. Alerts</h6>
                    <p class="small text-muted">Set up alerts to be notified when spending exceeds budget thresholds.</p>
                    
                    <hr>
                    
                    <h6><i class="bi bi-cpu text-warning"></i> 10. AI Analysis</h6>
                    <p class="small text-muted">Use AI-powered analysis to gain insights into your spending patterns.</p>
                    
                    <hr>

                    <h6><i class="bi bi-pie-chart text-warning"></i> 11. Charts</h6>
                    <p class="small text-muted">Visualize your financial data with various charts including monthly trends, account distribution, and variance analysis.</p>

                    <hr>

                    <h6><i class="bi bi-bar-chart text-warning"></i> 12. Power BI</h6>
                    <p class="small text-muted">Connect to Power BI for advanced business intelligence and reporting capabilities.</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-secondary text-white">
                    <i class="bi bi-gear"></i> Configuration
                </div>
                <div class="card-body">
                    <h6><i class="bi bi-gear text-secondary"></i> 13. Settings</h6>
                    <p class="small text-muted">Manage your profile and change your password. Admins can access additional system settings.</p>
                    
                    <hr>
                    
                    <h6><i class="bi bi-people text-secondary"></i> User Roles</h6>
                    <ul class="small text-muted list-unstyled">
                        <li><span class="badge bg-primary">Admin</span> Full access to all features</li>
                        <li><span class="badge bg-info">Finance</span> Can manage accounts, budgets, and imports</li>
                        <li><span class="badge bg-secondary">User</span> Can view budgets and record actuals</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-2">
        <div class="card-body bg-light">
            <h6><i class="bi bi-lightbulb text-info"></i> Quick Tips</h6>
            <ul class="small text-muted mb-0">
                <li>Use the navigation menu to switch between different sections</li>
                <li>Click on any row to view or edit details</li>
                <li>Use filters to narrow down your data views</li>
                <li>Export data using the export buttons where available</li>
            </ul>
        </div>
    </div>
</div>
@endsection