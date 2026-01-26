@extends('ypi.layout.admin_template')
@section('main')

<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('ypi.admin.guest') }}">Guest</a></li>
        <li class="breadcrumb-item active" aria-current="page">
            {{ $guestData->getFullNameAttribute() }}
        </li>
    </ol>
</nav>
<div class="row align-items-center justify-content-between g-3 mb-4">
    <div class="col-12 col-md-auto">
        <h2 class="mb-0">{{ $guestData->getFullNameAttribute() }}</h2>
    </div>
    <div class="col-12 col-md-auto d-flex">
        <a href="javascript:void(0);" id="edit_project_offcanv" data-id="{{ $guestData->id }}" data-table="page"
            class="btn btn-phoenix-secondary px-3 px-sm-5 me-2">
            <span class="fa-solid fa-edit me-sm-2"></span>
            <span class="d-none d-sm-inline">Edit </span>
        </a>
        <button class="btn btn-phoenix-danger me-2"><span class="fa-solid fa-trash me-2"></span><span>Delete
                Guest</span></button>
        <!-- <div>
                                    <button class="btn px-3 btn-phoenix-secondary" type="button" data-bs-toggle="dropdown"
                                        data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span
                                            class="fa-solid fa-ellipsis"></span></button>
                                    <ul class="dropdown-menu dropdown-menu-end p-0" style="z-index: 9999;">
                                        <li><a class="dropdown-item" href="#!">View profile</a></li>
                                        <li><a class="dropdown-item" href="#!">Report</a></li>
                                        <li><a class="dropdown-item" href="#!">Manage notifications</a></li>
                                        <li><a class="dropdown-item text-danger" href="#!">Delete Lead</a></li>
                                    </ul>
                                </div> -->
    </div>
</div>
<div class="row pb-9 gx-4">
    <div class="col-xl-10 pe-lg-2 flex-1">
        <ul class="nav nav-underline optionChainTableHeader gap-0 flex-nowrap scrollbar mb-4" id="stockDetailsTab" role="tablist">
            <li class="nav-item"> <a class="nav-link pt-0 text-nowrap active ps-0 pe-3 " id="tab-flight" href="#flight-tab" data-bs-toggle="tab" role="tab" aria-controls="flight-tab" aria-selected="true">Flights</a></li>
            <li class="nav-item"> <a class="nav-link pt-0 text-nowrap px-3 " id="tab-accomm" href="#accomm-tab" data-bs-toggle="tab" role="tab" aria-controls="accomm-tab" aria-selected="false">Accomodation</a></li>
            <li class="nav-item"> <a class="nav-link pt-0 text-nowrap px-3 " id="tab-finStates" href="#finStates-tab" data-bs-toggle="tab" role="tab" aria-controls="finStates-tab" aria-selected="false">Transportation</a></li>
            <li class="nav-item"> <a class="nav-link pt-0 text-nowrap px-3 " id="tab-forecast" href="#forecast-tab" data-bs-toggle="tab" role="tab" aria-controls="forecast-tab" aria-selected="false">Visa</a></li>
            <li class="nav-item"> <a class="nav-link pt-0 text-nowrap px-3 " id="tab-news" href="#news-tab" data-bs-toggle="tab" role="tab" aria-controls="news-tab" aria-selected="false">Dependents</a></li>
            <li class="nav-item"> <a class="nav-link pt-0 text-nowrap px-3 " id="tab-events" href="#events-tab" data-bs-toggle="tab" role="tab" aria-controls="events-tab" aria-selected="false">Documents</a></li>
            <li class="nav-item"> <a class="nav-link pt-0 text-nowrap px-3 " id="tab-comProfile" href="#comProfile-tab" data-bs-toggle="tab" role="tab" aria-controls="comProfile-tab" aria-selected="false">Profile</a></li>
            <li class="nav-item flex-1 d-none d-md-inline d-xl-none d-xxl-inline"> <a class="nav-link pt-0 text-nowrap px-3 disabled h-100" id="tab-empty1" href="#empty1-tab" data-bs-toggle="tab" role="tab" aria-selected="false"></a></li>
        </ul>
        <div class="tab-content" id="stockDetailsTabContent">
            <div class="tab-pane fade show active" id="flight-tab" role="tabpanel" aria-labelledby="tab-flight">
                <div class="d-flex justify-content-between m-2">
                    <!-- <div class="row flex-between-center g-3 mb-4"> -->
                    <div class="col-auto">
                        <h4>Flights </h4>
                        <!-- <p class="text-body-tertiary mb-0">Updated inventory according to the sales report.</p> -->
                    </div>
                    <div>
                        <x-formy.button_insert_js title='Add Flight' selectionId="offcanvas-add-flight" dataId="0" table="flight_table" />
                        <button class="btn px-3 btn-phoenix-secondary" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#bookingFilterOffcanvas" aria-haspopup="true" aria-expanded="false"
                            data-bs-reference="parent"><svg class="svg-inline--fa fa-filter text-primary" data-fa-transform="down-3"
                                aria-hidden="true" focusable="false" data-prefix="fas" data-icon="filter" role="img"
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""
                                style="transform-origin: 0.5em 0.6875em;">
                                <g transform="translate(256 256)">
                                    <g transform="translate(0, 96)  scale(1, 1)  rotate(0 0 0)">
                                        <path fill="currentColor"
                                            d="M3.9 54.9C10.5 40.9 24.5 32 40 32H472c15.5 0 29.5 8.9 36.1 22.9s4.6 30.5-5.2 42.5L320 320.9V448c0 12.1-6.8 23.2-17.7 28.6s-23.8 4.3-33.5-3l-64-48c-8.1-6-12.8-15.5-12.8-25.6V320.9L9 97.3C-.7 85.4-2.8 68.8 3.9 54.9z"
                                            transform="translate(-256 -256)"></path>
                                    </g>
                                </g>
                            </svg><!-- <span class="fa-solid fa-filter text-primary" data-fa-transform="down-3"></span> Font Awesome fontawesome.com -->
                        </button>
                    </div>

                </div>
                <div class="mb-0">
                    <x-ypi.admin.flight-card :projectId="$guestData->id" />
                </div>
            </div>
            <div class="tab-pane fade" id="accomm-tab" role="tabpanel" aria-labelledby="tab-accomm">
                <div class="tab-pane fade show active" id="flight-tab" role="tabpanel" aria-labelledby="tab-flight">
                    <div class="d-flex justify-content-between m-2">
                        <!-- <div class="row flex-between-center g-3 mb-4"> -->
                        <div class="col-auto">
                            <h4>Accommodations </h4>
                            <!-- <p class="text-body-tertiary mb-0">Updated inventory according to the sales report.</p> -->
                        </div>
                        <div>
                            <x-formy.button_insert_js title='Add Accommodation' selectionId="offcanvas-add-accomm" dataId="0" table="accomm_table" />
                            <button class="btn px-3 btn-phoenix-secondary" type="button" data-bs-toggle="offcanvas"
                                data-bs-target="#bookingFilterOffcanvas" aria-haspopup="true" aria-expanded="false"
                                data-bs-reference="parent"><svg class="svg-inline--fa fa-filter text-primary" data-fa-transform="down-3"
                                    aria-hidden="true" focusable="false" data-prefix="fas" data-icon="filter" role="img"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""
                                    style="transform-origin: 0.5em 0.6875em;">
                                    <g transform="translate(256 256)">
                                        <g transform="translate(0, 96)  scale(1, 1)  rotate(0 0 0)">
                                            <path fill="currentColor"
                                                d="M3.9 54.9C10.5 40.9 24.5 32 40 32H472c15.5 0 29.5 8.9 36.1 22.9s4.6 30.5-5.2 42.5L320 320.9V448c0 12.1-6.8 23.2-17.7 28.6s-23.8 4.3-33.5-3l-64-48c-8.1-6-12.8-15.5-12.8-25.6V320.9L9 97.3C-.7 85.4-2.8 68.8 3.9 54.9z"
                                                transform="translate(-256 -256)"></path>
                                        </g>
                                    </g>
                                </svg><!-- <span class="fa-solid fa-filter text-primary" data-fa-transform="down-3"></span> Font Awesome fontawesome.com -->
                            </button>
                        </div>

                    </div>
                    <div class="mb-0">
                        <x-ypi.admin.accommodation-card :projectId="$guestData->id" />
                    </div>
                </div>

            </div>
            <div class="tab-pane fade" id="finStates-tab" role="tabpanel" aria-labelledby="tab-finStates">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3 flex-between-center mb-4">
                            <div class="col-auto">
                                <h4>Apple Income Statement</h4>
                                <p class="text-body-tertiary mb-0">Financials in millions USD. </p>
                            </div>
                            <div class="col-auto">
                                <div class="d-flex align-items-center gap-2">
                                    <select class="form-select form-select-sm" id="amount" name="amount">
                                        <option value="million">Millions </option>
                                        <option value="billions">Thousands </option>
                                        <option value="remove"> hundreds </option>
                                    </select>
                                    <select class="form-select form-select-sm" id="time" name="time">
                                        <option value="million">Annual </option>
                                        <option value="semi-annual">Semi Annual </option>
                                        <option value="quarterly"> Quarterly </option>
                                    </select>
                                    <button class="btn btn-sm btn-phoenix-secondary"> <span class="fas fa-download"></span></button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive scrollbar">
                            <table class="table border-top border-translucent fs-9 mb-0">
                                <thead>
                                    <tr class="text-uppercase">
                                        <th class="fw-bold ps-0 py-3" style="min-width: 22rem;">Breakdown</th>
                                        <th class="fw-bold text-center bg-body-highlight py-3" style="min-width: 7.5rem;">ttm </th>
                                        <th class="fw-bold text-center py-3" style="min-width: 7.5rem;">2023-12-31</th>
                                        <th class="fw-bold text-center bg-body-highlight py-3" style="min-width: 7.5rem;">2022-12-31</th>
                                        <th class="fw-bold text-center py-3" style="min-width: 7.5rem;">2021-12-31</th>
                                        <th class="fw-bold text-center bg-body-highlight py-3" style="min-width: 7.5rem;">2020-12-31</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center fw-semibold">
                                    <tr>
                                        <td class="text-start">Total Revenue</td>
                                        <td class="bg-body-highlight">5,40,512</td>
                                        <td>5,21,250</td>
                                        <td class="bg-body-highlight">4,55,579</td>
                                        <td>3,93,488</td>
                                        <td class="bg-body-highlight">3,40,199</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Cost of Revenue</td>
                                        <td class="bg-body-highlight">90,010</td>
                                        <td>86,012</td>
                                        <td class="bg-body-highlight">75,221</td>
                                        <td>60,812</td>
                                        <td class="bg-body-highlight">47,164</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Gross Profit</td>
                                        <td class="bg-body-highlight">4,50,502</td>
                                        <td>4,35,238</td>
                                        <td class="bg-body-highlight">3,80,358</td>
                                        <td>3,32,676</td>
                                        <td class="bg-body-highlight">2,93,035</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start fw-bold"> <a class="btn px-0 d-block collapse-indicator py-0" data-bs-toggle="collapse" href="#collapseOperating" role="button" aria-expanded="true" aria-controls="collapseOperating">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="fs-9 text-body-highlight">Operating Expenses</div><span class="fa-solid fa-angle-down toggle-icon text-body-secondary"></span>
                                                </div>
                                            </a></td>
                                        <td class="bg-body-highlight"></td>
                                        <td> </td>
                                        <td class="bg-body-highlight"></td>
                                        <td> </td>
                                        <td class="bg-body-highlight"></td>
                                    </tr>
                                    <tr>
                                        <td class="py-0 border-bottom-0" colspan="6">
                                            <div class="collapse show" id="collapseOperating">
                                                <table class="table mb-0">
                                                    <tbody>
                                                        <tr class="bg-primary-subtle">
                                                            <td class="text-start ps-3 ps-xl-4" style="width: 22rem;">Selling General and Administration</td>
                                                            <td class="bg-body-highlight" style="width: 7.5rem;">33,981</td>
                                                            <td style="width: 7.5rem;">40,445</td>
                                                            <td class="bg-body-highlight" style="width: 7.5rem;">28,598</td>
                                                            <td style="width: 7.5rem;">37,770</td>
                                                            <td class="bg-body-highlight" style="width: 7.5rem;">31,635</td>
                                                        </tr>
                                                        <tr class="fw-bold bg-primary-subtle">
                                                            <td class="text-start fw-bolder ps-3 ps-xl-4">Total Operating Expenses</td>
                                                            <td class="bg-body-highlight">35,464</td>
                                                            <td>42,712</td>
                                                            <td class="bg-body-highlight">31,063</td>
                                                            <td>39,720</td>
                                                            <td class="bg-body-highlight">33,354</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="fw-bold">
                                        <td class="text-start fw-bolder">Operating Income or Loss</td>
                                        <td class="bg-body-highlight">4,15,038</td>
                                        <td>3,92,526</td>
                                        <td class="bg-body-highlight">3,49,295</td>
                                        <td>2,92,956</td>
                                        <td class="bg-body-highlight">2,59,681</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Interest Expense</td>
                                        <td class="bg-body-highlight">81,305</td>
                                        <td>72,864</td>
                                        <td class="bg-body-highlight">45,183</td>
                                        <td>39,393</td>
                                        <td class="bg-body-highlight">33,610</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Total Other Income/Expenses Net</td>
                                        <td class="bg-body-highlight">-98,893</td>
                                        <td>-1,92,510</td>
                                        <td class="bg-body-highlight">-2,07,796</td>
                                        <td>12,98,095</td>
                                        <td class="bg-body-highlight">2,72,800</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Income Before Tax</td>
                                        <td class="bg-body-highlight">2,36,758</td>
                                        <td>1,25,632</td>
                                        <td class="bg-body-highlight">88,256</td>
                                        <td>1,45,256</td>
                                        <td class="bg-body-highlight">2,45,563</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Income Tax Expense</td>
                                        <td class="bg-body-highlight">7,256</td>
                                        <td>-9,653</td>
                                        <td class="bg-body-highlight">-69,586</td>
                                        <td>2,41,012</td>
                                        <td class="bg-body-highlight">4,25,365</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Income from Continuing Operations</td>
                                        <td class="bg-body-highlight">2,12,356</td>
                                        <td>1,45,258</td>
                                        <td class="bg-body-highlight">25,365</td>
                                        <td>45,362</td>
                                        <td class="bg-body-highlight">4,16,259</td>
                                    </tr>
                                    <tr class="fw-bold">
                                        <td class="text-start fw-bolder">Net Income</td>
                                        <td class="bg-body-highlight">2,25,653</td>
                                        <td>4,58,693</td>
                                        <td class="bg-body-highlight">1,25,489</td>
                                        <td>5,36,125</td>
                                        <td class="bg-body-highlight">47,852</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Net Income Available to Common</td>
                                        <td class="bg-body-highlight">2,25,235</td>
                                        <td>1,36,665</td>
                                        <td class="bg-body-highlight">1,55,256</td>
                                        <td>1,25,365</td>
                                        <td class="bg-body-highlight">3,65,259</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Basic EPS</td>
                                        <td class="bg-body-highlight">-0.25</td>
                                        <td>2.15</td>
                                        <td class="bg-body-highlight">2.36</td>
                                        <td>20.47</td>
                                        <td class="bg-body-highlight">6.85</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Diluted EPS</td>
                                        <td class="bg-body-highlight">-0.25</td>
                                        <td>2.14</td>
                                        <td class="bg-body-highlight">2.36</td>
                                        <td>20.47</td>
                                        <td class="bg-body-highlight">6.97</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Basic Average Shares</td>
                                        <td class="bg-body-highlight">63,785</td>
                                        <td>63,700</td>
                                        <td class="bg-body-highlight">64,582</td>
                                        <td>64,142</td>
                                        <td class="bg-body-highlight">63,125</td>
                                    </tr>
                                    <tr>
                                        <td class="text-start">Diluted Average Shares</td>
                                        <td class="bg-body-highlight">63,455</td>
                                        <td>63,900</td>
                                        <td class="bg-body-highlight">65,256</td>
                                        <td>64,400</td>
                                        <td class="bg-body-highlight">61,475</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="forecast-tab" role="tabpanel" aria-labelledby="tab-forecast">
                <div class="row g-3 g-lg-5 flex-between-center mb-4">
                    <div class="col-auto">
                        <h4>Economic Prediction</h4>
                        <p class="text-body-tertiary mb-0">Brief summary of all projects </p>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex align-items-center gap-2">
                            <select class="form-select form-select-sm" id="forecast-amount" name="amount">
                                <option value="million">Annual </option>
                                <option value="billions">Half Annual </option>
                                <option value="remove"> Quarterly </option>
                            </select>
                            <select class="form-select form-select-sm" id="operations" name="operations">
                                <option value="export">Export </option>
                                <option value="view">View </option>
                                <option value="remove"> Remove </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row g-0">
                            <div class="col-sm-6 col-xxl-3 pb-4 border-bottom border-bottom-xxl-0 border-end-sm pe-sm-4  py-xxl-0">
                                <h5 class="text-body-highlight mb-3">Revenue This Year</h5>
                                <div class="row flex-between-center">
                                    <div class="col-9 pe-xl-0 order-xxl-1">
                                        <h4 class="mb-2">$185.10B</h4>
                                        <div class="d-flex align-items-center gap-2">
                                            <h6 class="text-body-tertiary fw-semibold mb-0 text-nowrap">From 171.84B </h6>
                                            <div class="badge badge-phoenix fs-10 badge-phoenix-success">7.71%<span class="fas ms-1 text-success-darker fa-chevron-up"></span></div>
                                        </div>
                                    </div>
                                    <div class="col-3 col-xxl-12 mb-xxl-3 ps-0 ps-xxl-3 d-flex justify-content-end justify-content-xxl-start">
                                        <div class="echart-revenue-this-year-chart revenue-this-year-chart"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xxl-3 border-bottom border-bottom-xxl-0 border-end-xxl ps-sm-4 pe-sm-4 py-4 pe-xl-3 pe-xxl-4 pt-sm-0 pb-xxl-0">
                                <h5 class="text-body-highlight mb-3">Revenue Next Year</h5>
                                <div class="row flex-between-center">
                                    <div class="col-9 pe-xl-0 order-xxl-1">
                                        <h4 class="mb-2">$200.210B</h4>
                                        <div class="d-flex align-items-center gap-2">
                                            <h6 class="text-body-tertiary fw-semibold mb-0 text-nowrap">From 185.10B </h6>
                                            <div class="badge badge-phoenix fs-10 badge-phoenix-success">0.06%<span class="fas ms-1 text-success-darker fa-chevron-up"></span></div>
                                        </div>
                                    </div>
                                    <div class="col-3 col-xxl-12 mb-xxl-3 ps-0 ps-xxl-3 d-flex justify-content-end justify-content-xxl-start">
                                        <div class="echart-revenue-next-year-chart revenue-next-year-chart"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xxl-3 border-bottom border-bottom-sm-0 border-end-sm pe-sm-4 px-xxl-4 py-4 pb-sm-0 py-xxl-0">
                                <h5 class="text-body-highlight mb-3">EPS This Year</h5>
                                <div class="row flex-between-center">
                                    <div class="col-9 pe-xl-0 order-xxl-1">
                                        <h4 class="mb-2">$10.39</h4>
                                        <div class="d-flex align-items-center gap-2">
                                            <h6 class="text-body-tertiary fw-semibold mb-0 text-nowrap">From 7.32 </h6>
                                            <div class="badge badge-phoenix fs-10 badge-phoenix-success">41.95%<span class="fas ms-1 text-success-darker fa-chevron-up"></span></div>
                                        </div>
                                    </div>
                                    <div class="col-3 col-xxl-12 mb-xxl-3 ps-0 ps-xxl-3 d-flex justify-content-end justify-content-xxl-start">
                                        <div class="echart-eps-this-year-chart eps-this-year-chart"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-xxl-3 ps-sm-4 pt-4 pe-sm-4 pt-xxl-0">
                                <h5 class="text-body-highlight mb-3">EPS Next Year</h5>
                                <div class="row flex-between-center">
                                    <div class="col-9 pe-xl-0 order-xxl-1">
                                        <h4 class="mb-2">$8.30</h4>
                                        <div class="d-flex align-items-center gap-2">
                                            <h6 class="text-body-tertiary fw-semibold mb-0 text-nowrap">From 10.39 </h6>
                                            <div class="badge badge-phoenix fs-10 badge-phoenix-danger">6.9%<span class="fas ms-1 text-danger-darker fa-chevron-down"></span></div>
                                        </div>
                                    </div>
                                    <div class="col-3 col-xxl-12 mb-xxl-3 ps-0 ps-xxl-3 d-flex justify-content-end justify-content-xxl-start">
                                        <div class="echart-eps-next-year-chart eps-next-year-chart"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-5" id="forecast" data-list='{"valueNames":["endingYear","revenue","revenueGrowth","eps","epsGrowth","forwardPE","noAnalysts"],"page":6}'>
                    <div class="table-responsive scrollbar">
                        <table class="table fs-9 mb-0 border-top border-translucent">
                            <thead>
                                <tr class="text-uppercase">
                                    <th class="white-space-nowrap fs-9 ps-0 align-middle">
                                        <div class="form-check mb-0 fs-8">
                                            <input class="form-check-input" id="checkbox-bulk-forecast-select" type="checkbox" data-bulk-select='{"body":"table-financial-forecast-body"}' />
                                        </div>
                                    </th>
                                    <th class="sort white-space-nowrap align-middle" scope="col" style="min-width:7.5rem;" data-sort="endingYear">Ending year</th>
                                    <th class="sort align-middle" scope="col" data-sort="revenue" style="min-width:7.5rem;">revenue</th>
                                    <th class="sort align-middle" scope="col" data-sort="revenueGrowth" style="min-width:7.5rem;">revenue growth</th>
                                    <th class="sort align-middle ps-5" scope="col" style="min-width:7.5rem;" data-sort="eps">eps</th>
                                    <th class="sort ps-5 align-middle" scope="col" style="min-width:7.5rem;" data-sort="epsGrowth">eps growth</th>
                                    <th class="sort ps-5 align-middle" scope="col" style="min-width:7.5rem;" data-sort="forwardPE">forward pe</th>
                                    <th class="sort ps-5 align-middle" scope="col" style="min-width:7.5rem;" data-sort="noAnalysts">no. analysts</th>
                                    <th class="sort pe-0 align-middle" scope="col" style="min-width: 3rem;"></th>
                                </tr>
                            </thead>
                            <tbody class="list" id="table-financial-forecast-body">
                                <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                    <td class="fs-9 align-middle ps-0">
                                        <div class="form-check mb-0 fs-8">
                                            <input class="form-check-input" type="checkbox" data-bulk-select-row='{"endingYear":"Sep 28, 2019","revenue":"$137.24B","revenueGrowth":"-2.04","eps":"4.57","epsGrowth":"-0.34","forwardPE":"N/A","noAnalysts":"N/A","revenueGrownDirection":false,"epsGrowthDirection":false}' />
                                        </div>
                                    </td>
                                    <td class="align-middle endingYear white-space-nowrap">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">Sep 28, 2019</p>
                                    </td>
                                    <td class="align-middle revenue white-space-nowrap">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">$$137.24B</p>
                                    </td>
                                    <td class="align-middle revenueGrowth">
                                        <p class="fs-9 fw-semibold mb-0 text-danger-dark">-2.04</p>
                                    </td>
                                    <td class="align-middle eps ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">4.57</p>
                                    </td>
                                    <td class="align-middle epsGrowth white-space-nowrap ps-5">
                                        <p class="fs-9 fw-semibold mb-0 text-danger-dark">-0.34</p>
                                    </td>
                                    <td class="align-middle forwardPE ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">N/A</p>
                                    </td>
                                    <td class="align-middle noANalysts ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">N/A</p>
                                    </td>
                                    <td class="align-middle white-space-nowrap pe-0">
                                        <div class="btn-reveal-trigger position-static">
                                            <button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10"></span></button>
                                            <div class="dropdown-menu dropdown-menu-end py-2"><a class="dropdown-item" href="#!">View</a><a class="dropdown-item" href="#!">Export</a>
                                                <div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#!">Remove</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                    <td class="fs-9 align-middle ps-0">
                                        <div class="form-check mb-0 fs-8">
                                            <input class="form-check-input" type="checkbox" data-bulk-select-row='{"endingYear":"Sep 26, 2020","revenue":"$122.49B","revenueGrowth":"5.51","eps":"4.33","epsGrowth":"10.44","forwardPE":"N/A","noAnalysts":"N/A","revenueGrownDirection":true,"epsGrowthDirection":true}' />
                                        </div>
                                    </td>
                                    <td class="align-middle endingYear white-space-nowrap">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">Sep 26, 2020</p>
                                    </td>
                                    <td class="align-middle revenue white-space-nowrap">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">$$122.49B</p>
                                    </td>
                                    <td class="align-middle revenueGrowth">
                                        <p class="fs-9 fw-semibold mb-0 text-success-dark">5.51</p>
                                    </td>
                                    <td class="align-middle eps ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">4.33</p>
                                    </td>
                                    <td class="align-middle epsGrowth white-space-nowrap ps-5">
                                        <p class="fs-9 fw-semibold mb-0 text-success-dark">10.44</p>
                                    </td>
                                    <td class="align-middle forwardPE ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">N/A</p>
                                    </td>
                                    <td class="align-middle noANalysts ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">N/A</p>
                                    </td>
                                    <td class="align-middle white-space-nowrap pe-0">
                                        <div class="btn-reveal-trigger position-static">
                                            <button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10"></span></button>
                                            <div class="dropdown-menu dropdown-menu-end py-2"><a class="dropdown-item" href="#!">View</a><a class="dropdown-item" href="#!">Export</a>
                                                <div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#!">Remove</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                    <td class="fs-9 align-middle ps-0">
                                        <div class="form-check mb-0 fs-8">
                                            <input class="form-check-input" type="checkbox" data-bulk-select-row='{"endingYear":"Sep 25, 2021","revenue":"$127.00B","revenueGrowth":"33.26","eps":"6.70","epsGrowth":"71.04","forwardPE":"N/A","noAnalysts":"N/A","revenueGrownDirection":true,"epsGrowthDirection":true}' />
                                        </div>
                                    </td>
                                    <td class="align-middle endingYear white-space-nowrap">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">Sep 25, 2021</p>
                                    </td>
                                    <td class="align-middle revenue white-space-nowrap">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">$$127.00B</p>
                                    </td>
                                    <td class="align-middle revenueGrowth">
                                        <p class="fs-9 fw-semibold mb-0 text-success-dark">33.26</p>
                                    </td>
                                    <td class="align-middle eps ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">6.70</p>
                                    </td>
                                    <td class="align-middle epsGrowth white-space-nowrap ps-5">
                                        <p class="fs-9 fw-semibold mb-0 text-success-dark">71.04</p>
                                    </td>
                                    <td class="align-middle forwardPE ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">N/A</p>
                                    </td>
                                    <td class="align-middle noANalysts ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">N/A</p>
                                    </td>
                                    <td class="align-middle white-space-nowrap pe-0">
                                        <div class="btn-reveal-trigger position-static">
                                            <button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10"></span></button>
                                            <div class="dropdown-menu dropdown-menu-end py-2"><a class="dropdown-item" href="#!">View</a><a class="dropdown-item" href="#!">Export</a>
                                                <div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#!">Remove</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                    <td class="fs-9 align-middle ps-0">
                                        <div class="form-check mb-0 fs-8">
                                            <input class="form-check-input" type="checkbox" data-bulk-select-row='{"endingYear":"Sep 24, 2022","revenue":"$156.74B","revenueGrowth":"7.79","eps":"6.13","epsGrowth":"8.91","forwardPE":"N/A","noAnalysts":"N/A","revenueGrownDirection":true,"epsGrowthDirection":true}' />
                                        </div>
                                    </td>
                                    <td class="align-middle endingYear white-space-nowrap">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">Sep 24, 2022</p>
                                    </td>
                                    <td class="align-middle revenue white-space-nowrap">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">$$156.74B</p>
                                    </td>
                                    <td class="align-middle revenueGrowth">
                                        <p class="fs-9 fw-semibold mb-0 text-success-dark">7.79</p>
                                    </td>
                                    <td class="align-middle eps ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">6.13</p>
                                    </td>
                                    <td class="align-middle epsGrowth white-space-nowrap ps-5">
                                        <p class="fs-9 fw-semibold mb-0 text-success-dark">8.91</p>
                                    </td>
                                    <td class="align-middle forwardPE ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">N/A</p>
                                    </td>
                                    <td class="align-middle noANalysts ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">N/A</p>
                                    </td>
                                    <td class="align-middle white-space-nowrap pe-0">
                                        <div class="btn-reveal-trigger position-static">
                                            <button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10"></span></button>
                                            <div class="dropdown-menu dropdown-menu-end py-2"><a class="dropdown-item" href="#!">View</a><a class="dropdown-item" href="#!">Export</a>
                                                <div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#!">Remove</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                    <td class="fs-9 align-middle ps-0">
                                        <div class="form-check mb-0 fs-8">
                                            <input class="form-check-input" type="checkbox" data-bulk-select-row='{"endingYear":"Sep 30, 2023","revenue":"$171.84B","revenueGrowth":"-2.80","eps":"7.32","epsGrowth":"0.33","forwardPE":"N/A","noAnalysts":"N/A","revenueGrownDirection":false,"epsGrowthDirection":true}' />
                                        </div>
                                    </td>
                                    <td class="align-middle endingYear white-space-nowrap">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">Sep 30, 2023</p>
                                    </td>
                                    <td class="align-middle revenue white-space-nowrap">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">$$171.84B</p>
                                    </td>
                                    <td class="align-middle revenueGrowth">
                                        <p class="fs-9 fw-semibold mb-0 text-danger-dark">-2.80</p>
                                    </td>
                                    <td class="align-middle eps ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">7.32</p>
                                    </td>
                                    <td class="align-middle epsGrowth white-space-nowrap ps-5">
                                        <p class="fs-9 fw-semibold mb-0 text-success-dark">0.33</p>
                                    </td>
                                    <td class="align-middle forwardPE ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">N/A</p>
                                    </td>
                                    <td class="align-middle noANalysts ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">N/A</p>
                                    </td>
                                    <td class="align-middle white-space-nowrap pe-0">
                                        <div class="btn-reveal-trigger position-static">
                                            <button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10"></span></button>
                                            <div class="dropdown-menu dropdown-menu-end py-2"><a class="dropdown-item" href="#!">View</a><a class="dropdown-item" href="#!">Export</a>
                                                <div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#!">Remove</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                    <td class="fs-9 align-middle ps-0">
                                        <div class="form-check mb-0 fs-8">
                                            <input class="form-check-input" type="checkbox" data-bulk-select-row='{"endingYear":"Sep 30, 2024","revenue":"$185.10B","revenueGrowth":"3.86","eps":"10.39","epsGrowth":"11.54","forwardPE":"N/A","noAnalysts":"N/A","revenueGrownDirection":true,"epsGrowthDirection":true}' />
                                        </div>
                                    </td>
                                    <td class="align-middle endingYear white-space-nowrap">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">Sep 30, 2024</p>
                                    </td>
                                    <td class="align-middle revenue white-space-nowrap">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">$$185.10B</p>
                                    </td>
                                    <td class="align-middle revenueGrowth">
                                        <p class="fs-9 fw-semibold mb-0 text-success-dark">3.86</p>
                                    </td>
                                    <td class="align-middle eps ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">10.39</p>
                                    </td>
                                    <td class="align-middle epsGrowth white-space-nowrap ps-5">
                                        <p class="fs-9 fw-semibold mb-0 text-success-dark">11.54</p>
                                    </td>
                                    <td class="align-middle forwardPE ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">N/A</p>
                                    </td>
                                    <td class="align-middle noANalysts ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">N/A</p>
                                    </td>
                                    <td class="align-middle white-space-nowrap pe-0">
                                        <div class="btn-reveal-trigger position-static">
                                            <button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10"></span></button>
                                            <div class="dropdown-menu dropdown-menu-end py-2"><a class="dropdown-item" href="#!">View</a><a class="dropdown-item" href="#!">Export</a>
                                                <div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#!">Remove</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                                    <td class="fs-9 align-middle ps-0">
                                        <div class="form-check mb-0 fs-8">
                                            <input class="form-check-input" type="checkbox" data-bulk-select-row='{"endingYear":"Sep 30, 2025","revenue":"$185.20B","revenueGrowth":"7.96","eps":"10.30","epsGrowth":"11.55","forwardPE":"32.54","noAnalysts":"46","revenueGrownDirection":true,"epsGrowthDirection":true}' />
                                        </div>
                                    </td>
                                    <td class="align-middle endingYear white-space-nowrap">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">Sep 30, 2025</p>
                                    </td>
                                    <td class="align-middle revenue white-space-nowrap">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">$$185.20B</p>
                                    </td>
                                    <td class="align-middle revenueGrowth">
                                        <p class="fs-9 fw-semibold mb-0 text-success-dark">7.96</p>
                                    </td>
                                    <td class="align-middle eps ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">10.30</p>
                                    </td>
                                    <td class="align-middle epsGrowth white-space-nowrap ps-5">
                                        <p class="fs-9 fw-semibold mb-0 text-success-dark">11.55</p>
                                    </td>
                                    <td class="align-middle forwardPE ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">32.54</p>
                                    </td>
                                    <td class="align-middle noANalysts ps-5">
                                        <p class="fs-9 fw-semibold text-body-emphasis mb-0">46</p>
                                    </td>
                                    <td class="align-middle white-space-nowrap pe-0">
                                        <div class="btn-reveal-trigger position-static">
                                            <button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10"></span></button>
                                            <div class="dropdown-menu dropdown-menu-end py-2"><a class="dropdown-item" href="#!">View</a><a class="dropdown-item" href="#!">Export</a>
                                                <div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#!">Remove</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row g-3 g-lg-5 mb-5">
                    <div class="col-xxl-6">
                        <div class="row g-3 g-lg-5 flex-between-center">
                            <div class="col-auto">
                                <h4>Forecast of Revenue</h4>
                                <p class="mb-0">Understanding Dividend Income Basics</p>
                            </div>
                            <div class="col-auto">
                                <div class="btn-reveal-trigger position-static">
                                    <button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal btn-phoenix-secondary" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10"></span></button>
                                    <div class="dropdown-menu dropdown-menu-end py-2"><a class="dropdown-item" href="#!">View</a><a class="dropdown-item" href="#!">Export</a>
                                        <div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#!">Remove</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="echart-forecast-of-revenue-chart" style="min-height: 300px;"></div>
                    </div>
                    <div class="col-xxl-6">
                        <div class="row g-3 g-lg-5 flex-between-center">
                            <div class="col-auto">
                                <h4>Growth in Revenue</h4>
                                <p class="mb-0">No. of bookings fulfilled &amp; cancelled</p>
                            </div>
                            <div class="col-auto">
                                <div class="btn-reveal-trigger position-static">
                                    <button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal btn-phoenix-secondary" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10"></span></button>
                                    <div class="dropdown-menu dropdown-menu-end py-2"><a class="dropdown-item" href="#!">View</a><a class="dropdown-item" href="#!">Export</a>
                                        <div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#!">Remove</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="echart-growth-in-revenue-chart" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="news-tab" role="tabpanel" aria-labelledby="tab-news">
                <div class="row g-3 g-lg-5 flex-between-center">
                    <div class="col-auto">
                        <h4 class="fw-bolder">Apple Stock News</h4>
                        <p class="mb-0 text-body-tertiary">Brief summary of all projects </p>
                    </div>
                    <div class="col-auto">
                        <div class="d-flex align-items-center gap-2">
                            <select class="form-select form-select-sm" id="news-filter" name="news-filter" style="max-width: 140px;">
                                <option value="all">All News </option>
                                <option value="orcl">Orcl News</option>
                                <option value="AAPL">AAPL News</option>
                            </select>
                            <div class="search-box w-100">
                                <form class="position-relative">
                                    <input class="form-control search-input search" type="search" placeholder="Search news" aria-label="Search" />
                                    <span class="fas fa-search search-box-icon"></span>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="events-tab" role="tabpanel" aria-labelledby="tab-events">
                <div class="row g-3 g-md-5 flex-between-center mb-3">
                    <div class="col-auto">
                        <h4 class="fw-bolder">Upcoming Events</h4>
                        <p class="mb-0 text-body-tertiary">Brief summary of all projects </p>
                    </div>
                    <div class="col-12 col-sm-auto">
                        <div class="search-box w-100">
                            <form class="position-relative">
                                <input class="form-control search-input search" type="search" placeholder="Search events" aria-label="Search" />
                                <span class="fas fa-search search-box-icon"></span>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="comProfile-tab" role="tabpanel" aria-labelledby="tab-comProfile">
                <div class="card mb-5">
                    <div class="card-body">
                        <div class="row g-0">
                            <div class="col-6 col-lg-12 col-xxl-6 pb-3 border-bottom border-end border-end-lg-0 border-end-xxl pe-3 pe-lg-0 pe-xxl-5">
                                <div class="row flex-between-center g-2">
                                    <div class="col-md-6">
                                        <div class="d-md-flex align-items-center gap-2">
                                            <div class="border bg-opacity-15 d-flex flex-center p-2 rounded-1 mb-3 mb-md-0 bg-info border-info-light" style="width: 2rem; height: 2rem;"><span class="fa-solid fa-user text-info-dark"></span></div>
                                            <h5 class="text-body-highlight mb-0 line-clamp-1">Total Employees</h5>
                                        </div>
                                    </div>
                                    <div class="col-1 d-none d-md-block">
                                        <h5 class="text-body-secondary mb-0">:</h5>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="d-flex justify-content-md-between align-items-center gap-2">
                                            <p class="mb-0 text-body-secondary">1.61K</p>
                                            <div class="badge badge-phoenix fs-10 badge-phoenix-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="From 1.64k">-1.83%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-12 col-xxl-6 pb-3  pt-lg-3 pt-xxl-0 ps-3 ps-lg-0 ps-xxl-5 border-bottom">
                                <div class="row flex-between-center g-2">
                                    <div class="col-md-6">
                                        <div class="d-md-flex align-items-center gap-2">
                                            <div class="border bg-opacity-15 d-flex flex-center p-2 rounded-1 mb-3 mb-md-0 bg-primary border-primary-light" style="width: 2rem; height: 2rem;"><span class="fa-solid fa-hand-holding-dollar text-primary-dark"></span></div>
                                            <h5 class="text-body-highlight mb-0 line-clamp-1">Total Revenue</h5>
                                        </div>
                                    </div>
                                    <div class="col-1 d-none d-md-block">
                                        <h5 class="text-body-secondary mb-0">:</h5>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="d-flex justify-content-md-between align-items-center gap-2">
                                            <p class="mb-0 text-body-secondary">$2.40M</p>
                                            <div class="badge badge-phoenix fs-10 badge-phoenix-success" data-bs-toggle="tooltip" data-bs-placement="top" title="From 5.4k">+4.71%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-12 col-xxl-6 py-3 border-bottom border-end border-end-lg-0 border-end-xxl pe-3 pe-lg-0 pe-xxl-5">
                                <div class="row flex-between-center g-2">
                                    <div class="col-md-6">
                                        <div class="d-md-flex align-items-center gap-2">
                                            <div class="border bg-opacity-15 d-flex flex-center p-2 rounded-1 mb-3 mb-md-0 bg-warning border-warning-light" style="width: 2rem; height: 2rem;"><span class="fa-solid fa-repeat text-warning-dark"></span></div>
                                            <h5 class="text-body-highlight mb-0 line-clamp-1">Total Change (1Y)</h5>
                                        </div>
                                    </div>
                                    <div class="col-1 d-none d-md-block">
                                        <h5 class="text-body-secondary mb-0">:</h5>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="d-flex justify-content-md-between align-items-center gap-2">
                                            <p class="mb-0 text-body-secondary">-3,000</p>
                                            <div class="badge badge-phoenix fs-10 badge-phoenix-success" data-bs-toggle="tooltip" data-bs-placement="top" title="From 2.64k">+1.71%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-12 col-xxl-6 py-3 ps-3 ps-lg-0 ps-xxl-5 border-bottom">
                                <div class="row flex-between-center g-2">
                                    <div class="col-md-6">
                                        <div class="d-md-flex align-items-center gap-2">
                                            <div class="border bg-opacity-15 d-flex flex-center p-2 rounded-1 mb-3 mb-md-0 bg-info border-info-light" style="width: 2rem; height: 2rem;"><span class="fa-solid fa-money-bill-trend-up text-info-dark"></span></div>
                                            <h5 class="text-body-highlight mb-0 line-clamp-1">Total Profits</h5>
                                        </div>
                                    </div>
                                    <div class="col-1 d-none d-md-block">
                                        <h5 class="text-body-secondary mb-0">:</h5>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="d-flex justify-content-md-between align-items-center gap-2">
                                            <p class="mb-0 text-body-secondary">$6.34M</p>
                                            <div class="badge badge-phoenix fs-10 badge-phoenix-success" data-bs-toggle="tooltip" data-bs-placement="top" title="From 3.64k">+3.71%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-12 col-xxl-6 py-3 pb-md-0 pb-lg-3 pb-xxl-0 border-end border-end-lg-0 border-end-xxl pe-3 pe-lg-0 pe-xxl-5 border-bottom-lg border-bottom-xxl-0">
                                <div class="row flex-between-center g-2">
                                    <div class="col-md-6">
                                        <div class="d-md-flex align-items-center gap-2">
                                            <div class="border bg-opacity-15 d-flex flex-center p-2 rounded-1 mb-3 mb-md-0 bg-primary border-primary-light" style="width: 2rem; height: 2rem;"><span class="fa-solid fa-chart-line text-primary-dark"></span></div>
                                            <h5 class="text-body-highlight mb-0 line-clamp-1">Total Growth (1Y)</h5>
                                        </div>
                                    </div>
                                    <div class="col-1 d-none d-md-block">
                                        <h5 class="text-body-secondary mb-0">:</h5>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="d-flex justify-content-md-between align-items-center gap-2">
                                            <p class="mb-0 text-body-secondary">-1.83%</p>
                                            <div class="badge badge-phoenix fs-10 badge-phoenix-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="From 1.4k">-2.32%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-12 col-xxl-6 pt-3 ps-3 ps-lg-0 ps-xxl-5">
                                <div class="row flex-between-center g-2">
                                    <div class="col-md-6">
                                        <div class="d-md-flex align-items-center gap-2">
                                            <div class="border bg-opacity-15 d-flex flex-center p-2 rounded-1 mb-3 mb-md-0 bg-warning border-warning-light" style="width: 2rem; height: 2rem;"><span class="fa-solid fa-chart-column text-warning-dark"></span></div>
                                            <h5 class="text-body-highlight mb-0 line-clamp-1">Total Market Cap</h5>
                                        </div>
                                    </div>
                                    <div class="col-1 d-none d-md-block">
                                        <h5 class="text-body-secondary mb-0">:</h5>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="d-flex justify-content-md-between align-items-center gap-2">
                                            <p class="mb-0 text-body-secondary">$3.46T</p>
                                            <div class="badge badge-phoenix fs-10 badge-phoenix-success" data-bs-toggle="tooltip" data-bs-placement="top" title="From 3.64k">+3.71%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="mb-3 text-body">Company Description</h4>
                        <p class="mb-2">Apple Inc. creates, produces, and sells wearable technology, tablets, smartphones, PCs, and accessories all over the world.</p>
                        <p class="mb-0">The company sells wearables, home goods, and accessories like AirPods, Apple TV, Apple Watch, Beats devices, and HomePod. It also offers a line of cellphones called iPhone, a line of personal computers called Mac, and a line of tablets called iPad...<a href="#!">read more</a></p>
                        <hr class="my-4" />
                        <div class="row g-0">
                            <div class="col-12 col-md-6 pe-md-4 pb-4 pb-md-0">
                                <h4 class="text-body mb-3">Company Details </h4>
                                <div class="row gx-5 gx-md-3 gx-lg-1 gx-xl-3 gx-xxl-5">
                                    <div class="col-sm-6 col-md-6 col-xl-12 col-xxl-6 pe-md-2">
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-1"><span class="fa-solid fs-8 fa-building" style="width: 16px;"></span>
                                                <h5 class="my-2 line-clamp-1 ms-2">Company Name</h5>
                                            </div>
                                            <div class="ps-4">
                                                <div class="d-flex align-items-center gap-2"> <img class="img-fluid d-dark-none" src="../../assets/img/brand3/dark_apple_logo.png" alt="img" /><img class="img-fluid d-light-none" src="../../assets/img/brand3/light_apple_logo.png" alt="dark-image" />
                                                    <p class="mb-0 text-body-secondary">Apple Inc</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-1"><span class="fa-solid fs-8 fa-briefcase" style="width: 16px;"></span>
                                                <h5 class="my-2 line-clamp-1 ms-2">CTO</h5>
                                            </div>
                                            <div class="ps-4">
                                                <p class="mb-0 text-body-secondary">Timothy Cook</p>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-1"><span class="fa-solid fs-8 fa-earth-americas" style="width: 16px;"></span>
                                                <h5 class="my-2 line-clamp-1 ms-2">Country</h5>
                                            </div>
                                            <div class="ps-4">
                                                <p class="mb-0 text-body-secondary">United States</p>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-1"><span class="fa-solid fs-8 fa-flag" style="width: 16px;"></span>
                                                <h5 class="my-2 line-clamp-1 ms-2">Founded</h5>
                                            </div>
                                            <div class="ps-4">
                                                <p class="mb-0 text-body-secondary">1997</p>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-1"><span class="fa-solid fs-8 fa-calendar-check" style="width: 16px;"></span>
                                                <h5 class="my-2 line-clamp-1 ms-2">IOP Date</h5>
                                            </div>
                                            <div class="ps-4">
                                                <p class="mb-0 text-body-secondary">Dec 12, 1980</p>
                                            </div>
                                        </div>
                                        <div class="mb-3 mb-sm-0 mb-md-3 mb-lg-0 mb-xl-3 mb-xxl-0">
                                            <div class="d-flex align-items-center mb-1"><span class="fa-solid fs-8 fa-city" style="width: 16px;"></span>
                                                <h5 class="my-2 line-clamp-1 ms-2">Industry</h5>
                                            </div>
                                            <div class="ps-4">
                                                <p class="mb-0 text-body-secondary">Consumer Electronics</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6 col-xl-12 col-xxl-6 ps-md-2">
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-1"><span class="fa-solid fs-8 fa-chart-pie" style="width: 16px;"></span>
                                                <h5 class="my-2 line-clamp-1 ms-2">Sector</h5>
                                            </div>
                                            <div class="ps-4">
                                                <p class="mb-0 text-body-secondary">Technology</p>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-1"><span class="fa-solid fs-8 fa-users" style="width: 16px;"></span>
                                                <h5 class="my-2 line-clamp-1 ms-2">Employees</h5>
                                            </div>
                                            <div class="ps-4">
                                                <p class="mb-0 text-body-secondary">1,61,000</p>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-1"><span class="fa-solid fs-8 fa-globe" style="width: 16px;"></span>
                                                <h5 class="my-2 line-clamp-1 ms-2">Website</h5>
                                            </div>
                                            <div class="ps-4"> <a href="https://apple.com">apple.com</a>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-1"><span class="fa-solid fs-8 fa-phone" style="width: 16px;"></span>
                                                <h5 class="my-2 line-clamp-1 ms-2">Phone Number</h5>
                                            </div>
                                            <div class="ps-4">
                                                <p class="mb-0 text-body-secondary">+1234567890</p>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center mb-1"><span class="fa-solid fs-8 fa-location" style="width: 16px;"></span>
                                                <h5 class="my-2 line-clamp-1 ms-2">Address</h5>
                                            </div>
                                            <div class="ps-4">
                                                <p class="mb-0 text-body-secondary">One Apple Park Way, Cupertino, CA 95014</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 ps-md-4 pt-4 pt-md-0 border-top border-top-md-0 border-start-md">
                                <h4 class="text-body mb-3">Stock Details</h4>
                                <div class="row gx-5 gx-md-3 gx-lg-1 gx-xl-3 gx-xxl-5">
                                    <div class="col-sm-6 col-md-6 col-xl-12 col-xxl-6">
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-1"><span class="fa-solid fs-8 fa-ticket" style="width: 16px;"></span>
                                                <h5 class="my-2 line-clamp-1 ms-2">Ticker Symbol</h5>
                                            </div>
                                            <div class="ps-4">
                                                <p class="mb-0 text-body-secondary">AAPL</p>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-1"><span class="fa-solid fs-8 fa-right-left" style="width: 16px;"></span>
                                                <h5 class="my-2 line-clamp-1 ms-2">Exchange</h5>
                                            </div>
                                            <div class="ps-4">
                                                <p class="mb-0 text-body-secondary">NASDAQ</p>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-1"><span class="fa-solid fs-8 fa-calendar-week" style="width: 16px;"></span>
                                                <h5 class="my-2 line-clamp-1 ms-2">Fiscal Year</h5>
                                            </div>
                                            <div class="ps-4">
                                                <p class="mb-0 text-body-secondary">October - September</p>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-1"><span class="fa-solid fs-8 fa-chart-line" style="width: 16px;"></span>
                                                <h5 class="my-2 line-clamp-1 ms-2">Reporting Cur.</h5>
                                            </div>
                                            <div class="ps-4">
                                                <p class="mb-0 text-body-secondary">USD</p>
                                            </div>
                                        </div>
                                        <div class="mb-3 mb-sm-0 mb-md-3 mb-lg-0 mb-xl-3 mb-xxl-0">
                                            <div class="d-flex align-items-center mb-1"><span class="fa-solid fs-8 fa-code-compare" style="width: 16px;"></span>
                                                <h5 class="my-2 line-clamp-1 ms-2">CIK Code</h5>
                                            </div>
                                            <div class="ps-4">
                                                <p class="mb-0 text-body-secondary">0000320193</p>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-1"><span class="fa-solid fs-8 fa-hashtag" style="width: 16px;"></span>
                                                <h5 class="my-2 line-clamp-1 ms-2">CUSIP Number</h5>
                                            </div>
                                            <div class="ps-4">
                                                <p class="mb-0 text-body-secondary">037833100</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-6 col-xl-12 col-xxl-6">
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-1"><span class="fa-solid fs-8 fa-hashtag" style="width: 16px;"></span>
                                                <h5 class="my-2 line-clamp-1 ms-2">ISIN Number</h5>
                                            </div>
                                            <div class="ps-4">
                                                <p class="mb-0 text-body-secondary">US0378331005</p>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center mb-1"><span class="fa-solid fs-8 fa-id-card" style="width: 16px;"></span>
                                                <h5 class="my-2 line-clamp-1 ms-2">Employer ID</h5>
                                            </div>
                                            <div class="ps-4">
                                                <p class="mb-0 text-body-secondary">94-2404110</p>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center mb-1"><span class="fa-solid fs-8 fa-code" style="width: 16px;"></span>
                                                <h5 class="my-2 line-clamp-1 ms-2">SIC Code</h5>
                                            </div>
                                            <div class="ps-4">
                                                <p class="mb-0 text-body-secondary">3571</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row flex-between-center g-3 mb-4">
                    <div class="col-auto">
                        <h4>Chart of Employees</h4>
                        <p class="mb-0">No. of bookings fulfilled &amp; cancelled</p>
                    </div>
                    <div class="col-auto">
                        <div class="btn-group stock-btn-group" role="group" aria-label="employees-btn-group">
                            <button class="btn btn-phoenix-secondary">Total</button>
                            <button class="btn btn-phoenix-secondary">Change</button>
                            <button class="btn btn-phoenix-secondary active">Growth</button>
                        </div>
                    </div>
                </div>
                <div class="echart-company-profile-employees-chart mb-5" style="width: 100%; height: 300px;"></div>
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3 flex-between-center mb-3">
                            <div class="col-auto">
                                <h4>Employee Records</h4>
                                <p class="mb-0">Record of employees' roles and tenure.</p>
                            </div>
                            <div class="col-auto">
                                <select class="form-select form-select-sm" id="action" name="action">
                                    <option value="export">Export </option>
                                    <option value="import"> Import </option>
                                    <option value="delete"> Delete </option>
                                </select>
                            </div>
                        </div>
                        <div id="employeeRecord" data-list='{"valueNames":["date","employees","change","growth"],"page":10,"pagination":true}'>
                            <div class="table-responsive scrollbar">
                                <table class="table fs-9 mb-0 border-top border-translucent">
                                    <thead>
                                        <tr class="text-uppercase">
                                            <th class="sort white-space-nowrap align-middle ps-0" scope="col" style="min-width:14rem;" data-sort="date">date</th>
                                            <th class="sort align-middle text-center" scope="col" data-sort="employees" style="min-width:8rem;">employees</th>
                                            <th class="sort align-middle text-center" scope="col" data-sort="change" style="min-width:8rem;">Change</th>
                                            <th class="sort align-middle text-end" scope="col" style="min-width:11rem;" data-sort="growth">Growth</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list">
                                        <tr>
                                            <td class="align-middle date white-space-nowrap">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">Sep 30, 2023</p>
                                            </td>
                                            <td class="align-middle employees white-space-nowrap text-center">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">161,000</p>
                                            </td>
                                            <td class="align-middle text-center change">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">10,000</p>
                                            </td>
                                            <td class="align-middle text-end growth white-space-nowrap">
                                                <p class="fs-9 fw-semibold mb-0 text-danger">-1.83%</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-middle date white-space-nowrap">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">Sep 24, 2022</p>
                                            </td>
                                            <td class="align-middle employees white-space-nowrap text-center">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">164,000</p>
                                            </td>
                                            <td class="align-middle text-center change">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">-3,000</p>
                                            </td>
                                            <td class="align-middle text-end growth white-space-nowrap">
                                                <p class="fs-9 fw-semibold mb-0 text-success">6.49%</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-middle date white-space-nowrap">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">Sep 25, 2021</p>
                                            </td>
                                            <td class="align-middle employees white-space-nowrap text-center">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">154,000</p>
                                            </td>
                                            <td class="align-middle text-center change">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">7,000</p>
                                            </td>
                                            <td class="align-middle text-end growth white-space-nowrap">
                                                <p class="fs-9 fw-semibold mb-0 text-success">4.76%</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-middle date white-space-nowrap">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">Sep 26, 2020</p>
                                            </td>
                                            <td class="align-middle employees white-space-nowrap text-center">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">147,000</p>
                                            </td>
                                            <td class="align-middle text-center change">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">10,000</p>
                                            </td>
                                            <td class="align-middle text-end growth white-space-nowrap">
                                                <p class="fs-9 fw-semibold mb-0 text-success">7.30%</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-middle date white-space-nowrap">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">Sep 28, 2019</p>
                                            </td>
                                            <td class="align-middle employees white-space-nowrap text-center">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">137,000</p>
                                            </td>
                                            <td class="align-middle text-center change">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">5,000</p>
                                            </td>
                                            <td class="align-middle text-end growth white-space-nowrap">
                                                <p class="fs-9 fw-semibold mb-0 text-success">3.79%</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-middle date white-space-nowrap">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">Sep 29, 2018</p>
                                            </td>
                                            <td class="align-middle employees white-space-nowrap text-center">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">132,000</p>
                                            </td>
                                            <td class="align-middle text-center change">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">9,000</p>
                                            </td>
                                            <td class="align-middle text-end growth white-space-nowrap">
                                                <p class="fs-9 fw-semibold mb-0 text-success">7.32%</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-middle date white-space-nowrap">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">Sep 30, 2017</p>
                                            </td>
                                            <td class="align-middle employees white-space-nowrap text-center">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">123,000</p>
                                            </td>
                                            <td class="align-middle text-center change">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">7,000</p>
                                            </td>
                                            <td class="align-middle text-end growth white-space-nowrap">
                                                <p class="fs-9 fw-semibold mb-0 text-success">6.03%</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-middle date white-space-nowrap">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">Sep 26, 2016</p>
                                            </td>
                                            <td class="align-middle employees white-space-nowrap text-center">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">112,000</p>
                                            </td>
                                            <td class="align-middle text-center change">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">9,000</p>
                                            </td>
                                            <td class="align-middle text-end growth white-space-nowrap">
                                                <p class="fs-9 fw-semibold mb-0 text-success">4.30%</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-middle date white-space-nowrap">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">Sep 24, 2015</p>
                                            </td>
                                            <td class="align-middle employees white-space-nowrap text-center">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">109,000</p>
                                            </td>
                                            <td class="align-middle text-center change">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">8,000</p>
                                            </td>
                                            <td class="align-middle text-end growth white-space-nowrap">
                                                <p class="fs-9 fw-semibold mb-0 text-success">5.79%</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-middle date white-space-nowrap">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">Sep 18, 2014</p>
                                            </td>
                                            <td class="align-middle employees white-space-nowrap text-center">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">107,000</p>
                                            </td>
                                            <td class="align-middle text-center change">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">8,000</p>
                                            </td>
                                            <td class="align-middle text-end growth white-space-nowrap">
                                                <p class="fs-9 fw-semibold mb-0 text-success">5.77%</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-middle date white-space-nowrap">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">Sep 30, 2013</p>
                                            </td>
                                            <td class="align-middle employees white-space-nowrap text-center">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">100,000</p>
                                            </td>
                                            <td class="align-middle text-center change">
                                                <p class="fs-9 fw-semibold text-body-secondary mb-0">10,000</p>
                                            </td>
                                            <td class="align-middle text-end growth white-space-nowrap">
                                                <p class="fs-9 fw-semibold mb-0 text-success">6.03%</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="row align-items-center justify-content-between py-2 pe-0 fs-9 pagination-subtle">
                                <div class="col-auto d-flex">
                                    <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body" data-list-info="data-list-info"></p><a class="fw-semibold" href="#!" data-list-view="*">View all<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a><a class="fw-semibold d-none" href="#!" data-list-view="less">View Less<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                                </div>
                                <div class="col-auto d-flex">
                                    <button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                                    <ul class="mb-0 pagination"></ul>
                                    <button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row gap-3 g-0 flex-between-center mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-secondary py-3 border-y mt-4 position-sticky bottom-0 z-2 d-xl-none stock-details-footer">
            <div class="col-auto">
                <div class="d-flex align-items-center gap-2">
                    <h3 class="mb-0 text-body">$226.51</h3>
                    <div class="badge badge-phoenix badge-phoenix-success">+0.62 (0.27%)</div>
                </div>
            </div>
            <div class="col-12 col-sm-auto">
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-primary flex-1" id="offcanvasStockDetails" data-bs-toggle="offcanvas" data-bs-target="#stockDetailsSidebar" aria-controls="stockDetailsSidebar">Buy Share</button>
                    <button class="btn btn-phoenix-secondary"> <span class="fas fa-clock"> </span></button>
                    <button class="btn btn-phoenix-secondary"> <span class="fas fa-eye"> </span></button>
                </div>
            </div>
        </div>
    </div>
</div>

@include('ypi.admin.guest.modals.flight_modals')
@include('ypi.admin.guest.modals.accommodation_modals')
<script src="{{asset('assets/js/pages/gms/flight.js')}}"></script>
<script src="{{asset('assets/js/pages/gms/accomm.js')}}"></script>

@endsection

@push('script')
<script src="{{ asset('fnx/assets/js/pages/stock-details.js') }}"></script>

<script>
    // showing the offcanvas for the task creation
    $(document).ready(function() {
        console.log('ready');
        $('.dropify').dropify();

    });
</script>

@endpush