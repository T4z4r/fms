<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## Power BI Analytics Tutorial

This Financial Management System includes a comprehensive Power BI analytics dashboard that provides advanced business intelligence capabilities for budget and spending analysis.

### Accessing Power BI Analytics

1. **Navigate to Power BI**: Go to `/powerbi` in your browser after logging into the application
2. **Select Cost Centre**: Choose a cost centre from the dropdown to analyze
3. **Choose Year**: Select the year you want to analyze
4. **Apply Filters**: Use quarter and view mode filters as needed

### Key Features

#### Basic Analytics Dashboard
- **KPI Cards**: Total budget, actual spending, variance, and year-over-year growth
- **Monthly Trends**: Interactive budget vs actual spending charts
- **Department Comparison**: Cross-department spending analysis
- **Account Distribution**: Detailed breakdown by account
- **Yearly Comparison**: Multi-year spending trends
- **Variance Analysis**: Month-by-month budget variance tracking

#### Advanced Business Analytics

##### 1. Spending Velocity Analysis
- **Daily Velocity**: Calculates average daily spending rate
- **Acceleration Tracking**: Monitors spending acceleration patterns
- **Year-End Projections**: Predicts full-year spending based on current velocity

##### 2. Budget Utilization Rate
- **Account-Level Analysis**: Shows utilization percentage for each account
- **Risk Status Indicators**: Color-coded warnings for overspending
- **Remaining Budget**: Tracks available budget per account

##### 3. Seasonal Trend Analysis
- **Quarterly Patterns**: Analyzes spending patterns across Q1-Q4
- **Seasonal Indices**: Compares current year vs previous year seasonality
- **Trend Changes**: Identifies significant seasonal shifts

##### 4. Month-over-Month Growth
- **Growth Rate Calculations**: Tracks percentage changes between months
- **Growth Status**: Categorizes growth as stable, high growth, or decline
- **Trend Visualization**: Color-coded growth indicators

##### 5. Budget Accuracy Prediction
- **Historical Analysis**: Uses past 3 years of data for predictions
- **Accuracy Scoring**: Provides budget accuracy percentages
- **Variance Prediction**: Forecasts potential budget variances

##### 6. Risk Analysis & Alerts
- **Overspend Detection**: Identifies accounts at risk of exceeding budget
- **Risk Levels**: Critical, high, medium, and low risk classifications
- **Seasonal Risk**: Detects unusual seasonal spending patterns

##### 7. Category Breakdown
- **Automatic Categorization**: Groups accounts into categories:
  - Personnel (salaries, wages, payroll)
  - Facilities (rent, office, equipment)
  - Technology (software, hardware, IT)
  - Marketing (advertising, promotion)
  - Other (miscellaneous)
- **Percentage Analysis**: Shows category contribution to total spending

##### 8. Rolling Averages
- **3-Month Moving Average**: Smoothes short-term fluctuations
- **6-Month Moving Average**: Identifies longer-term trends
- **Trend Analysis**: Compares actual vs smoothed data

##### 9. Cumulative Spending Analysis
- **Year-to-Date Tracking**: Monitors cumulative spend vs budget
- **Efficiency Metrics**: Calculates spending efficiency percentages
- **Variance Tracking**: Tracks cumulative budget variances

##### 10. Anomaly Detection
- **Statistical Analysis**: Uses Z-score analysis to detect anomalies
- **Severity Classification**: Critical and warning level anomalies
- **Account-Level Detection**: Identifies unusual spending at account level

### Chart Types and Filters

#### Chart Type Switching
Each chart includes an export menu (hamburger icon) that allows switching between:
- **Line Charts**: Best for trend analysis
- **Column/Bar Charts**: Ideal for comparisons
- **Area Charts**: Good for cumulative data
- **Pie Charts**: Perfect for percentage breakdowns
- **Spline Charts**: Smoothed trend lines

#### Global Filters
- **Cost Centre**: Select specific department or business unit
- **Year**: Choose analysis year
- **Quarter**: Filter by specific quarters (Q1-Q4)
- **View Mode**: Interactive, Comparison, Trend, Forecast, Advanced Analytics, Risk Analysis
- **Chart Type**: Apply chart type globally across all visualizations

### Export and Reporting

#### PDF Export
- Click the PDF button to export all visible charts as a single PDF
- Includes all current filter settings and data

#### Excel Export
- Enhanced CSV export with multiple sheets:
  - Basic monthly data and yearly comparisons
  - Budget utilization rates and seasonal data
  - Month-over-month growth analysis
  - Category breakdowns and rolling averages
  - Cumulative spending and anomaly detection data

### Best Practices

1. **Start with Overview**: Begin with basic KPIs and monthly trends
2. **Use Filters Strategically**: Apply cost centre and year filters first
3. **Focus on Risks**: Check risk analysis alerts regularly
4. **Monitor Trends**: Use seasonal and rolling average analysis for pattern identification
5. **Export for Sharing**: Use PDF/Excel exports for stakeholder presentations
6. **Regular Review**: Set up regular review cycles for budget monitoring

### Advanced Tips

- **Custom Analysis**: Combine multiple analytics for comprehensive insights
- **Trend Spotting**: Use rolling averages to identify emerging patterns
- **Risk Prevention**: Monitor utilization rates to prevent overspending
- **Seasonal Planning**: Use seasonal analysis for budget planning
- **Anomaly Investigation**: Investigate anomalies to understand root causes
- **Predictive Insights**: Use velocity and prediction analytics for forecasting

The Power BI analytics dashboard transforms raw financial data into actionable business intelligence, enabling data-driven decision making and proactive budget management.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
