<template>
  <div style="height: 75vh">
      <ui-box title="Filters" class="mb-3">
        <template v-slot:body>
          <form ref="filtersForm">

            <div class="form-row">
              <div class="form-group col-md-6">
                <label>Search</label>
                <input v-model="filters.query" type="email" class="form-control" placeholder="Filter by email or name">
              </div>

              <div class="form-group col-md-6">
                <label>State</label>
                <select v-model="filters.state" class="form-control">
                  <option value="">All</option>
                  <option v-for="option in states" :key="option">{{ option }}</option>
                </select>
              </div>

            </div>
            <button type="submit" class="btn btn-primary" @click.prevent="applyFilters()">Search</button>

          </form>
        </template>
      </ui-box>

      <template v-if="madeApiCall && loadedSubscribers==0">
        <div class="alert alert-info">No results.</div>
      </template>
    <template v-else>
      <p>
        <span
            class="text-muted">Showing <strong>{{ loadedSubscribers }}</strong> results out of <strong>{{
            totalSubscribers
          }}</strong>.</span>
      </p>
    </template>
      <ag-grid-vue style="width: 100%; height: 100%;"
                   class="ag-theme-alpine"
                   :gridOptions="gridOptions"
                   :columnDefs="columnDefs"
                   :rowData="rowData"
                   :singleClickEdit="true"
                   :paginationPageSize="paginationPageSize"
                   rowModelType="infinite"
                   :cacheOverflowSize="cacheOverflowSize"
                   :maxConcurrentDatasourceRequests="maxConcurrentDatasourceRequests"
                   :infiniteInitialRowCount="infiniteInitialRowCount"
                   :maxBlocksInCache="maxBlocksInCache"
                   :rowBuffer="rowBuffer"
                   :cacheBlockSize="perPage"
                   @grid-ready="onGridReady"
                   enableCellTextSelection="true"
                   suppressMultiSort="true"
      >
      </ag-grid-vue>


  </div>
</template>

<script>
import {AgGridVue} from "ag-grid-vue";
import StateRenderer from "@/components/StateRenderer";
import StateEditor from "@/components/StateEditor";
import SubscribersRowActions from "@/components/SubscribersRowActions";

import Api from "@/services/Api.js";
import EventBus from "@/services/EventBus.js";

const PER_PAGE = 50;
const STATES = [
  'active',
  'unconfirmed',
  'junk',
  'bounced'
];

export default {
  name: 'subscribersPage',
  data() {
    return {
      gridOptions: null,
      gridApi: null,
      columnDefs: null,
      rowData: null,
      paginationPageSize: 20,
      rowBuffer: null,
      cacheOverflowSize: null,
      maxConcurrentDatasourceRequests: null,
      infiniteInitialRowCount: null,
      maxBlocksInCache: null,
      perPage: PER_PAGE,

      loadedSubscribers: 0,
      totalSubscribers: 0,
      madeApiCall: false,

      filters: {
        query: null,
        state: ''
      },
      states: STATES,

      cursor: 0,
      lastRow:0,
      lastKnownId:0
    }
  },
  components: {
    AgGridVue,
    StateRenderer,
    StateEditor,
    SubscribersRowActions
  },

  beforeMount() {
    this.gridOptions = {
      context: {
        vueApp: this
      }
    };
    this.columnDefs = [
      {headerName: 'Name', field: 'name', flex: 1, sortable: true},
      {headerName: 'Email', field: 'email', flex: 1, sortable: true},
      {headerName: 'Source', field: 'source', flex: 1, sortable: true},
      {
        headerName: 'State',
        field: 'state',
        editable: true,
        cellRendererFramework: 'StateRenderer',
        cellEditorFramework: 'StateEditor',
        flex: 1,
      },
      {
        headerName: 'Actions',
        field: 'id',
        cellRendererFramework: 'SubscribersRowActions'
      },
    ];

    this.rowBuffer = 0;
    this.paginationPageSize = 20;
    this.cacheOverflowSize = 5;
    this.maxConcurrentDatasourceRequests = 2;
    this.infiniteInitialRowCount = 20;
    this.maxBlocksInCache = 10;

  },

  mounted() {
    this.gridApi = this.gridOptions.api;
    this.gridColumnApi = this.gridOptions.columnApi;

    EventBus.$on('subscriberRowAction:deleted', (selectedData) => {
      this.gridApi.refreshInfiniteCache();
    })
  },

  methods: {
    applyFilters() {
      this.cursor = 0;
      this.lastKnownId = 0;
      this.lastRow = 0;
      this.loadedSubscribers=0;
      this.gridApi.setInfiniteRowCount(0)
      this.gridApi.purgeInfiniteCache();
      this.madeApiCall = false;
    },

    async getServerData() {
      this.madeApiCall = true;
      return await Api.getSubscribers({
        cursor: this.cursor,
        take: this.perPage,
        query: this.filters.query,
        state: this.filters.state == '' ? null : this.filters.state
      });
    },

    onGridReady(params) {
      console.log('onGridReady');
      let dataSource = _=>{
        return {
          getRows: async (params) => {
            console.log('get row');
            let result = await params.context.vueApp.getServerData();

            let data = result.data;
            let meta = result.meta;

            if(data.length > 0)
            {
              let lastKnownId = data[data.length-1].id;

              params.context.vueApp.cursor = lastKnownId;
              params.context.vueApp.more = true;

              let lastRow = params.context.vueApp.lastRow + data.length;

              params.context.vueApp.lastRow = lastRow;
              params.context.vueApp.loadedSubscribers = lastRow;
              params.context.vueApp.totalSubscribers = meta.total;
              params.successCallback(data, meta.total);

            }
            else
            {
              params.successCallback([{columnNameField: "No results found."}], 0);
            }

            return data;

          }
        }
      }

      // create the datasource for your grid
      var data = dataSource();

      // set the grid datasource
      params.api.setDatasource(data);
    }
  }
}
</script>
