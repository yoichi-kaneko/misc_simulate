import * as React from 'react';
import { InlineMath } from 'react-katex';
import 'katex/dist/katex.min.css';

interface NashReportProps {
  a_rho: string;
}

const NashReport: React.FC<NashReportProps> = ({ a_rho }) => {
  return (
    <div className="card pd-20 report_block">
      <h6 className="tx-12 tx-uppercase tx-info tx-bold mg-b-15">Report</h6>
      <div className="d-flex mg-b-10">
        <div className="bd-r pd-l-12">
          <label className="tx-12">
            <InlineMath math="a({\rho})" />
          </label>
          <p className="tx-lato tx-inverse tx-bold">
            <span>{a_rho}</span>
          </p>
        </div>
      </div>
    </div>
  );
};

export default NashReport;